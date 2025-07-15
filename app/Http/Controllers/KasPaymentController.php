<?php

namespace App\Http\Controllers;

use App\Models\Kas;
use App\Models\PaymentInfo;
use App\Models\Notifikasi;
use App\Models\Rt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class KasPaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the payment form for a specific kas.
     */
    public function showPaymentForm(Kas $kas)
    {
        $user = Auth::user();
        if ($user->role !== 'masyarakat' || $kas->penduduk_id !== $user->penduduk->id) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk halaman pembayaran ini.');
        }

        if ($kas->status === 'lunas' || $kas->status === 'menunggu_konfirmasi') {
            return redirect()->route('kas.index')->with('info', 'Kas ini sudah lunas atau sedang menunggu konfirmasi.');
        }

        $paymentInfo = PaymentInfo::where('rt_id', $kas->rt_id)->first();

        return view('kas.payment-form', compact('kas', 'paymentInfo'));
    }

    /**
     * Submit payment for a specific kas.
     */
    public function submitPayment(Request $request, Kas $kas)
    {
        $user = Auth::user();
        if ($user->role !== 'masyarakat' || $kas->penduduk_id !== $user->penduduk->id) {
            return response()->json(['success' => false, 'message' => 'Anda tidak memiliki akses untuk melakukan pembayaran ini.'], 403);
        }

        if ($kas->status === 'lunas' || $kas->status === 'menunggu_konfirmasi') {
            return response()->json(['success' => false, 'message' => 'Kas ini sudah lunas atau sedang menunggu konfirmasi.'], 400);
        }

        $validator = Validator::make($request->all(), [
            'metode_bayar' => 'required|string|max:50',
            'bukti_bayar' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120', // 5MB max
            'bukti_bayar_notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $path = $request->file('bukti_bayar')->store('public/bukti_bayar');
            $kas->update([
                'status' => 'menunggu_konfirmasi',
                'metode_bayar' => $request->metode_bayar,
                'bukti_bayar_file' => str_replace('public/', 'storage/', $path),
                'bukti_bayar_uploaded_at' => now(),
                'bukti_bayar_notes' => $request->bukti_bayar_notes,
            ]);

            // Create notification for RT/RW/Admin
            $this->createConfirmationNotification($kas);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Bukti pembayaran berhasil diunggah. Menunggu konfirmasi.'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error submitting payment: ' . $e->getMessage(), [
                'kas_id' => $kas->id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengunggah bukti pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display payment success page.
     */
    public function paymentSuccess(Kas $kas)
    {
        $user = Auth::user();
        if ($user->role !== 'masyarakat' || $kas->penduduk_id !== $user->penduduk->id) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }
        return view('kas.payment-success', compact('kas'));
    }

    /**
     * Display payments list for RT/RW/Kades/Admin.
     */
    public function paymentsList(Request $request)
    {
        $user = Auth::user();
        $query = Kas::with(['penduduk', 'rt.rw', 'confirmedBy']);

        // Apply role-based filters
        switch ($user->role) {
            case 'masyarakat':
                if ($user->penduduk) {
                    $query->where('penduduk_id', $user->penduduk->id);
                } else {
                    return redirect()->back()->with('error', 'Data penduduk tidak ditemukan');
                }
                break;
            case 'rt':
                if ($user->penduduk && $user->penduduk->rtKetua) {
                    $query->where('rt_id', $user->penduduk->rtKetua->id);
                } else {
                    return redirect()->back()->with('error', 'Data RT tidak ditemukan');
                }
                break;
            case 'rw':
                if ($user->penduduk && $user->penduduk->rwKetua) {
                    $rtIds = $user->penduduk->rwKetua->rts->pluck('id');
                    $query->whereIn('rt_id', $rtIds);
                } else {
                    return redirect()->back()->with('error', 'Data RW tidak ditemukan');
                }
                break;
            // admin dan kades bisa lihat semua
        }

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('rt_id')) {
            $query->where('rt_id', $request->rt_id);
        }
        if ($request->filled('metode_bayar')) {
            $query->where('metode_bayar', $request->metode_bayar);
        }
        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }
        if ($request->filled('minggu_ke')) {
            $query->where('minggu_ke', $request->minggu_ke);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('penduduk', function($q) use ($search) {
                $q->where('nama_lengkap', 'like', '%' . $search . '%')
                  ->orWhere('nik', 'like', '%' . $search . '%');
            });
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate(10);

        $rts = [];
        if (in_array($user->role, ['admin', 'kades'])) {
            $rts = Rt::all();
        } elseif ($user->role === 'rw' && $user->penduduk && $user->penduduk->rwKetua) {
            $rts = $user->penduduk->rwKetua->rts;
        } elseif ($user->role === 'rt' && $user->penduduk && $user->penduduk->rtKetua) {
            $rts = [$user->penduduk->rtKetua];
        }

        return view('payments.list', compact('payments', 'rts'));
    }

    /**
     * Confirm payment by RT/RW/Kades/Admin.
     */
    public function confirmPayment(Request $request, Kas $kas)
    {
        $user = Auth::user();
        
        if (!in_array($user->role, ['rt', 'rw', 'kades', 'admin'])) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        if (!$this->canAccessPayment($kas)) {
            return response()->json(['success' => false, 'message' => 'Anda tidak memiliki akses untuk kas ini'], 403);
        }

        if ($kas->status !== 'menunggu_konfirmasi') {
            return response()->json(['success' => false, 'message' => 'Kas ini tidak dalam status menunggu konfirmasi'], 400);
        }

        $validator = Validator::make($request->all(), [
            'confirmation_notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $kas->update([
                'status' => 'lunas',
                'tanggal_bayar' => now(),
                'confirmed_by' => $user->id,
                'confirmed_at' => now(),
                'confirmation_notes' => $request->confirmation_notes,
            ]);

            // Update RT saldo
            $rt = Rt::find($kas->rt_id);
            if ($rt) {
                $rt->saldo += $kas->jumlah;
                $rt->save();
            }

            // Create notification for masyarakat
            $this->createPaymentNotification($kas, 'approved');

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil dikonfirmasi',
                'data' => $kas->fresh(['penduduk', 'rt.rw', 'confirmedBy'])
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error confirming payment: ' . $e->getMessage(), [
                'kas_id' => $kas->id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengkonfirmasi pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show payment proof.
     */
    public function showProof(Kas $kas)
    {
        if (!$kas->bukti_bayar_file) {
            return redirect()->back()->with('error', 'Bukti pembayaran tidak ditemukan.');
        }
        return response()->file(storage_path('app/public/' . str_replace('storage/', '', $kas->bukti_bayar_file)));
    }

    /**
     * Download payment proof.
     */
    public function downloadProof(Kas $kas)
    {
        if (!$kas->bukti_bayar_file) {
            return redirect()->back()->with('error', 'Bukti pembayaran tidak ditemukan.');
        }
        return response()->download(storage_path('app/public/' . str_replace('storage/', '', $kas->bukti_bayar_file)));
    }

    /**
     * Helper function to check if user can access a specific payment.
     */
    private function canAccessPayment(Kas $payment)
    {
        $user = Auth::user();
        switch ($user->role) {
            case 'masyarakat':
                return $payment->penduduk_id === $user->penduduk->id;
            case 'rt':
                return $payment->rt_id === $user->penduduk->rtKetua->id;
            case 'rw':
                return $user->penduduk->rwKetua->rts->contains('id', $payment->rt_id);
            case 'admin':
            case 'kades':
                return true;
            default:
                return false;
        }
    }

    /**
     * Helper function to create payment notification
     */
    private function createPaymentNotification(Kas $payment, $status)
    {
        Notifikasi::create([
            'user_id' => $payment->penduduk->user->id ?? null,
            'judul' => 'Pembayaran Kas ' . ($status === 'approved' ? 'Disetujui' : 'Ditolak'),
            'pesan' => 'Pembayaran kas minggu ke-' . $payment->minggu_ke . ' telah ' . ($status === 'approved' ? 'disetujui' : ($status === 'pending' ? 'menunggu konfirmasi' : 'ditolak')),
            'tipe' => $status === 'approved' ? 'success' : ($status === 'pending' ? 'warning' : 'error'),
            'kategori' => 'pembayaran',
            'data' => json_encode([
                'kas_id' => $payment->id,
                'minggu_ke' => $payment->minggu_ke,
                'jumlah' => $payment->jumlah,
            ]),
            'dibaca' => false,
        ]);
    }

    /**
     * Helper function to create notification for RT/RW/Admin about pending payment.
     */
    private function createConfirmationNotification(Kas $payment)
    {
        // Notify RT Ketua
        if ($payment->rt && $payment->rt->ketua) {
            Notifikasi::create([
                'user_id' => $payment->rt->ketua->user_id,
                'judul' => 'Pembayaran Kas Menunggu Konfirmasi',
                'pesan' => 'Pembayaran kas dari ' . $payment->penduduk->nama_lengkap . ' (Minggu ke-' . $payment->minggu_ke . ') menunggu konfirmasi Anda.',
                'tipe' => 'warning',
                'kategori' => 'pembayaran',
                'data' => json_encode([
                    'kas_id' => $payment->id,
                    'penduduk_id' => $payment->penduduk->id,
                    'minggu_ke' => $payment->minggu_ke,
                    'jumlah' => $payment->jumlah,
                ]),
                'dibaca' => false,
            ]);
        }

        // Optionally notify RW Ketua or Admin if needed
        // ...
    }
}
