<?php

namespace App\Http\Controllers;

use App\Models\Kas;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use App\Models\PaymentInfo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Jobs\SendKasEmailNotification;

class KasPaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:masyarakat')->only(['showPaymentForm', 'submitPayment', 'paymentSuccess', 'processPayment']);
        $this->middleware('role:rt,rw,kades,admin')->only(['paymentsList', 'confirmPayment', 'showProof', 'downloadProof']);
    }

    public function showPaymentForm(Kas $kas)
    {
        $user = Auth::user();
        if ($user->role !== 'masyarakat' || ($user->penduduk && $kas->penduduk_id !== $user->penduduk->id)) {
            abort(403, 'Unauthorized action.');
        }

        if ($kas->status === 'lunas' || $kas->status === 'menunggu_konfirmasi') {
            return redirect()->route('dashboard.masyarakat')->with('error', 'Tagihan ini sudah lunas atau sedang menunggu konfirmasi.');
        }

        // Get payment info for user's RT
        $paymentInfo = null;
        if ($user->penduduk && $user->penduduk->kk && $user->penduduk->kk->rt) {
            $rtId = $user->penduduk->kk->rt->id;
            $paymentInfo = PaymentInfo::where('rt_id', $rtId)
                                  ->where('is_active', true)
                                  ->first();
        }

        return view('kas.payment-form', compact('kas', 'paymentInfo'));
    }

    public function processPayment(Request $request, Kas $kas)
    {
        $user = Auth::user();
        if ($user->role !== 'masyarakat' || ($user->penduduk && $kas->penduduk_id !== $user->penduduk->id)) {
            abort(403, 'Unauthorized action.');
        }

        if ($kas->status === 'lunas' || $kas->status === 'menunggu_konfirmasi') {
            return redirect()->route('dashboard.masyarakat')->with('error', 'Tagihan ini sudah lunas atau sedang menunggu konfirmasi.');
        }

        $request->validate([
            'metode_bayar' => 'required|string|max:255',
            'bukti_bayar_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'bukti_bayar_notes' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $filePath = null;
            if ($request->hasFile('bukti_bayar_file')) {
                $filePath = $request->file('bukti_bayar_file')->store('bukti_pembayaran', 'public');
            }

            $metodeBayar = Kas::normalizePaymentMethod($request->metode_bayar);
            $status = ($metodeBayar === 'tunai') ? 'lunas' : 'menunggu_konfirmasi';

            // Pastikan jumlah_dibayar dihitung dari nilai kas yang ada di database
            // atau dari nilai yang baru saja di-set jika ini adalah entri baru.
            // Menggunakan $kas->jumlah dan $kas->denda yang sudah ada di model.
            $jumlahDibayar = $kas->jumlah + ($kas->denda ?? 0);

            $kas->update([
                'status' => $status,
                'tanggal_bayar' => Carbon::now(),
                'jumlah_dibayar' => $jumlahDibayar, // Menggunakan variabel yang sudah dihitung
                'metode_bayar' => $metodeBayar,
                'bukti_bayar_file' => $filePath,
                'bukti_bayar_notes' => $request->bukti_bayar_notes,
                'bukti_bayar_uploaded_at' => Carbon::now(),
            ]);

            if ($status === 'lunas') {
                $kas->update([
                    'confirmed_by' => $user->id,
                    'confirmed_at' => Carbon::now(),
                    'confirmation_notes' => 'Pembayaran tunai langsung dikonfirmasi oleh masyarakat.',
                ]);

                $rt = $kas->rt;
                if ($rt && $kas->jumlah_dibayar > 0) {
                    $rt->saldo += $kas->jumlah_dibayar;
                    $rt->save();
                }
            } else {
                $this->createPaymentNotifications($kas);
            }

            DB::commit();
            return redirect()->route('kas.payment.success', $kas->id)->with('success', 'Pembayaran berhasil diproses.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error processing payment: ' . $e->getMessage());
            return back()->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage())->withInput();
        }
    }

    public function paymentSuccess(Kas $kas)
    {
        $user = Auth::user();
        if ($user->role !== 'masyarakat' || ($user->penduduk && $kas->penduduk_id !== $user->penduduk->id)) {
            abort(403, 'Unauthorized action.');
        }
        return view('kas.payment-success', compact('kas'));
    }

    public function paymentsList(Request $request)
    {
        $user = Auth::user();
        $query = Kas::with(['penduduk.user', 'rt.rw']);

        switch ($user->role) {
            case 'rt':
                if ($user->penduduk && $user->penduduk->rtKetua) {
                    $query->where('rt_id', $user->penduduk->rtKetua->id);
                } else {
                    return redirect()->back()->with('error', 'Data RT tidak ditemukan.');
                }
                break;
            case 'rw':
                if ($user->penduduk && $user->penduduk->rwKetua) {
                    $rtIds = $user->penduduk->rwKetua->rts->pluck('id');
                    $query->whereIn('rt_id', $rtIds);
                } else {
                    return redirect()->back()->with('error', 'Data RW tidak ditemukan.');
                }
                break;
            case 'kades':
            case 'admin':
                // No specific RT/RW filter for kades/admin
                break;
            default:
                abort(403, 'Unauthorized role.');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->whereIn('status', ['menunggu_konfirmasi', 'lunas', 'ditolak']);
        }

        $payments = $query->orderBy('bukti_bayar_uploaded_at', 'desc')->paginate(10);

        return view('payments.list', compact('payments'));
    }

    public function showProof(Kas $kas)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['rt', 'rw', 'kades', 'admin'])) {
            if ($user->role === 'masyarakat' && ($user->penduduk && $kas->penduduk_id !== $user->penduduk->id)) {
                abort(403, 'Unauthorized action.');
            } elseif ($user->role !== 'masyarakat') {
                abort(403, 'Unauthorized action.');
            }
        }

        if ($user->role === 'rt' && $kas->rt_id !== ($user->penduduk->rtKetua->id ?? null)) {
            abort(403, 'Unauthorized access.');
        } elseif ($user->role === 'rw') {
            $rtIdsInRw = ($user->penduduk->rwKetua->rts->pluck('id')->toArray() ?? []);
            if (!in_array($kas->rt_id, $rtIdsInRw)) {
                abort(403, 'Unauthorized access.');
            }
        }

        return view('payments.proof', compact('kas'));
    }

    public function downloadProof(Kas $kas)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['rt', 'rw', 'kades', 'admin'])) {
            if ($user->role === 'masyarakat' && ($user->penduduk && $kas->penduduk_id !== $user->penduduk->id)) {
                abort(403, 'Unauthorized action.');
            } elseif ($user->role !== 'masyarakat') {
                abort(403, 'Unauthorized action.');
            }
        }

        if ($user->role === 'rt' && $kas->rt_id !== ($user->penduduk->rtKetua->id ?? null)) {
            abort(403, 'Unauthorized access.');
        } elseif ($user->role === 'rw') {
            $rtIdsInRw = ($user->penduduk->rwKetua->rts->pluck('id')->toArray() ?? []);
            if (!in_array($kas->rt_id, $rtIdsInRw)) {
                abort(403, 'Unauthorized access.');
            }
        }

        if (!$kas->bukti_bayar_file) {
            return back()->with('error', 'Bukti pembayaran tidak ditemukan.');
        }

        $filePath = str_replace('storage/', 'public/', $kas->bukti_bayar_file);
        if (!Storage::exists($filePath)) {
            return back()->with('error', 'File bukti pembayaran tidak ditemukan di server.');
        }

        return Storage::download($filePath, 'bukti_pembayaran_kas_' . $kas->id . '.' . pathinfo($filePath, PATHINFO_EXTENSION));
    }

    public function confirmPayment(Request $request, Kas $kas)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['rt', 'rw', 'kades', 'admin'])) {
            abort(403, 'Unauthorized action.');
        }

        if ($user->role === 'rt' && $kas->rt_id !== ($user->penduduk->rtKetua->id ?? null)) {
            abort(403, 'Unauthorized access.');
        } elseif ($user->role === 'rw') {
            $rtIdsInRw = ($user->penduduk->rwKetua->rts->pluck('id')->toArray() ?? []);
            if (!in_array($kas->rt_id, $rtIdsInRw)) {
                abort(403, 'Unauthorized access.');
            }
        }

        if ($kas->status !== 'menunggu_konfirmasi') {
            return response()->json(['success' => false, 'message' => 'Pembayaran tidak dalam status menunggu konfirmasi.'], 400);
        }

        $request->validate([
            'action' => 'required|in:approve,reject',
            'catatan_konfirmasi' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            if ($request->action === 'approve') {
                // Calculate jumlah_dibayar before updating
                $jumlahDibayar = $kas->jumlah + ($kas->denda ?? 0);

                $kas->update([
                    'status' => 'lunas',
                    'tanggal_bayar' => $kas->tanggal_bayar ?? Carbon::now(),
                    'jumlah_dibayar' => $jumlahDibayar, // Set jumlah_dibayar di sini
                    'confirmed_by' => $user->id,
                    'confirmed_at' => Carbon::now(),
                    'confirmation_notes' => $request->catatan_konfirmasi ?? 'Pembayaran dikonfirmasi.',
                ]);
                $rt = $kas->rt;
                if ($rt && $kas->jumlah_dibayar > 0) {
                    $rt->saldo += $kas->jumlah_dibayar;
                    $rt->save();
                }

                // Send approved email notification menggunakan Job
                try {
                    SendKasEmailNotification::dispatch($kas, 'kas_approved', [
                        'payment_method' => $kas->metode_bayar,
                        'payment_date' => $kas->tanggal_bayar->toISOString(),
                        'confirmation_notes' => $request->catatan_konfirmasi
                    ]);
                    Log::info('Approved email notification dispatched', ['kas_id' => $kas->id]);
                } catch (\Exception $e) {
                    Log::error('Failed to dispatch approved email notification', [
                        'kas_id' => $kas->id,
                        'error' => $e->getMessage()
                    ]);
                }

                $message = 'Pembayaran kas Anda untuk minggu ke-' . $kas->minggu_ke . ' tahun ' . $kas->tahun . ' telah dikonfirmasi lunas.';
            } else { // This block handles 'reject'
                $kas->update([
                    'status' => 'ditolak',
                    'confirmed_by' => $user->id,
                    'confirmed_at' => Carbon::now(),
                    'confirmation_notes' => $request->catatan_konfirmasi ?? 'Pembayaran ditolak.',
                ]);

                // Send rejected email notification menggunakan Job
                try {
                    SendKasEmailNotification::dispatch($kas, 'kas_rejected', [
                        'rejection_reason' => $request->catatan_konfirmasi ?? 'Pembayaran ditolak.'
                    ]);
                    Log::info('Rejected email notification dispatched', ['kas_id' => $kas->id]);
                } catch (\Exception $e) {
                    Log::error('Failed to dispatch rejected email notification', [
                        'kas_id' => $kas->id,
                        'error' => $e->getMessage()
                    ]);
                }

                $message = 'Pembayaran kas Anda untuk minggu ke-' . $kas->minggu_ke . ' tahun ' . $kas->tahun . ' ditolak. Silakan periksa catatan konfirmasi.';
            }

            if ($kas->penduduk && $kas->penduduk->user) {
                Notifikasi::create([
                    'user_id' => $kas->penduduk->user->id,
                    'judul' => 'Status Pembayaran Kas',
                    'pesan' => $message,
                    'link' => route('kas.show', $kas->id),
                    'is_read' => false,
                ]);
            } else {
                Log::warning('Could not create notification for payment confirmation. Penduduk or User missing.', ['kas_id' => $kas->id, 'action' => $request->action]);
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Konfirmasi pembayaran berhasil.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error confirming payment: ' . $e->getMessage(), ['kas_id' => $kas->id, 'action' => $request->action, 'error_trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat memproses konfirmasi. Silakan coba lagi atau hubungi administrator.'], 500);
        }
    }

    private function createPaymentNotifications(Kas $kas)
    {
        $rtUser = $kas->rt->ketuaRt->user ?? null;
        $rwUser = $kas->rt->rw->ketuaRw->user ?? null;
        $adminUsers = \App\Models\User::where('role', 'admin')->get();

        $notificationMessage = 'Pembayaran kas dari ' . ($kas->penduduk->nama_lengkap ?? 'Warga') . ' untuk minggu ke-' . $kas->minggu_ke . ' tahun ' . $kas->tahun . ' menunggu konfirmasi.';

        if ($rtUser) {
            Notifikasi::create([
                'user_id' => $rtUser->id,
                'judul' => 'Konfirmasi Pembayaran Kas',
                'pesan' => $notificationMessage,
                'link' => route('payments.proof', $kas->id),
                'is_read' => false,
            ]);
        }
        if ($rwUser) {
            Notifikasi::create([
                'user_id' => $rwUser->id,
                'judul' => 'Konfirmasi Pembayaran Kas',
                'pesan' => $notificationMessage,
                'link' => route('payments.proof', $kas->id),
                'is_read' => false,
            ]);
        }
        foreach ($adminUsers as $adminUser) {
            Notifikasi::create([
                'user_id' => $adminUser->id,
                'judul' => 'Konfirmasi Pembayaran Kas',
                'pesan' => $notificationMessage,
                'link' => route('payments.proof', $kas->id),
                'is_read' => false,
            ]);
        }
    }
}
