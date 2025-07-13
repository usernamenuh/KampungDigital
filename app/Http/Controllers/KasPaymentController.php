<?php

namespace App\Http\Controllers;

use App\Models\Kas;
use App\Models\PaymentInfo;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KasPaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Submit payment proof from masyarakat
     */
    public function submitPayment(Request $request, Kas $kas)
    {
        $user = Auth::user();
        
        // Validasi akses - hanya masyarakat yang bisa submit payment
        if ($user->role !== 'masyarakat') {
            return redirect()->back()->with('error', 'Akses ditolak. Halaman ini khusus untuk masyarakat.');
        }

        // Validasi kepemilikan kas
        if (!$user->penduduk || $kas->penduduk_id !== $user->penduduk->id) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk kas ini.');
        }

        // Validasi status kas
        if ($kas->status === 'lunas') {
            return redirect()->route('kas.index')->with('info', 'Kas ini sudah lunas.');
        }

        if ($kas->status === 'menunggu_konfirmasi') {
            return redirect()->back()->with('info', 'Bukti pembayaran sudah dikirim sebelumnya. Menunggu konfirmasi dari RT.');
        }

        $request->validate([
            'metode_bayar' => 'required|in:tunai,bank_transfer,e_wallet,qr_code',
            'bukti_bayar' => 'required_unless:metode_bayar,tunai|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'bukti_bayar_notes' => 'nullable|string|max:500',
            'e_wallet_type' => 'required_if:metode_bayar,e_wallet|in:dana,gopay,ovo,shopeepay',
        ], [
            'metode_bayar.required' => 'Metode pembayaran harus dipilih.',
            'metode_bayar.in' => 'Metode pembayaran tidak valid.',
            'bukti_bayar.required_unless' => 'Bukti pembayaran harus diupload kecuali untuk pembayaran tunai.',
            'bukti_bayar.file' => 'Bukti pembayaran harus berupa file.',
            'bukti_bayar.mimes' => 'Format file harus JPG, JPEG, PNG, atau PDF.',
            'bukti_bayar.max' => 'Ukuran file maksimal 2MB.',
            'e_wallet_type.required_if' => 'Jenis e-wallet harus dipilih.',
            'bukti_bayar_notes.max' => 'Catatan maksimal 500 karakter.',
        ]);

        DB::beginTransaction();
        try {
            // Hitung denda jika terlambat
            $totalBayar = $kas->jumlah;
            $denda = 0;
            
            if ($kas->is_overdue && $kas->status === 'belum_bayar') {
                $pengaturanKas = \App\Models\PengaturanKas::first();
                if ($pengaturanKas && $pengaturanKas->persentase_denda > 0) {
                    $denda = ($kas->jumlah * $pengaturanKas->persentase_denda) / 100;
                    $totalBayar += $denda;
                }
            }

            // Handle file upload
            $buktiPath = null;
            if ($request->hasFile('bukti_bayar')) {
                $file = $request->file('bukti_bayar');
                $filename = 'bukti_' . $kas->id . '_' . time() . '.' . $file->getClientOriginalExtension();
                $buktiPath = $file->storeAs('bukti-bayar', $filename, 'public');
            }

            // Tentukan status berdasarkan metode pembayaran
            $status = 'menunggu_konfirmasi';
            $tanggalBayar = null;
            
            if ($request->metode_bayar === 'tunai') {
                $status = 'lunas';
                $tanggalBayar = now();
            }

            // Update kas
            $updateData = [
                'metode_bayar' => $request->metode_bayar,
                'bukti_bayar_file' => $buktiPath,
                'bukti_bayar_notes' => $request->bukti_bayar_notes,
                'bukti_bayar_uploaded_at' => now(),
                'status' => $status,
                'denda' => $denda,
            ];

            if ($request->metode_bayar === 'e_wallet') {
                $updateData['e_wallet_type'] = $request->e_wallet_type;
            }

            if ($status === 'lunas') {
                $updateData['tanggal_bayar'] = $tanggalBayar;
                $updateData['confirmed_by'] = $user->id;
                $updateData['confirmed_at'] = now();
                $updateData['confirmation_notes'] = 'Pembayaran tunai langsung dikonfirmasi.';
            }

            $kas->update($updateData);

            // Buat notifikasi untuk RT jika bukan pembayaran tunai
            if ($status === 'menunggu_konfirmasi') {
                $this->createPaymentNotification($kas, 'submitted');
            }

            DB::commit();

            $message = $status === 'lunas' 
                ? 'Pembayaran tunai berhasil dicatat dan langsung dikonfirmasi.' 
                : 'Bukti pembayaran berhasil dikirim. Menunggu konfirmasi dari RT.';

            return redirect()->route('payments.success', $kas)->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();
            
            // Hapus file jika ada error
            if ($buktiPath && Storage::disk('public')->exists($buktiPath)) {
                Storage::disk('public')->delete($buktiPath);
            }
            
            return redirect()->back()->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Confirm payment by RT/RW/Kades/Admin
     */
    public function confirmPayment(Request $request, Kas $kas)
    {
        $user = Auth::user();
        
        // Validasi akses
        if (!in_array($user->role, ['rt', 'rw', 'kades', 'admin'])) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.']);
        }

        // Validasi akses kas
        if (!$this->canAccessKas($kas, $user)) {
            return response()->json(['success' => false, 'message' => 'Anda tidak memiliki akses untuk kas ini.']);
        }

        // Validasi status kas
        if ($kas->status !== 'menunggu_konfirmasi') {
            return response()->json(['success' => false, 'message' => 'Kas ini tidak dalam status menunggu konfirmasi.']);
        }

        $request->validate([
            'action' => 'required|in:approve,reject',
            'confirmation_notes' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            if ($request->action === 'approve') {
                // Approve pembayaran
                $kas->update([
                    'status' => 'lunas',
                    'tanggal_bayar' => now(),
                    'confirmed_by' => $user->id,
                    'confirmed_at' => now(),
                    'confirmation_notes' => $request->confirmation_notes,
                ]);

                $this->createPaymentNotification($kas, 'approved');
                $message = 'Pembayaran berhasil dikonfirmasi.';
                
            } else {
                // Reject pembayaran
                $oldBuktiFile = $kas->bukti_bayar_file;
                
                $kas->update([
                    'status' => 'belum_bayar',
                    'metode_bayar' => null,
                    'bukti_bayar_file' => null,
                    'bukti_bayar_notes' => null,
                    'bukti_bayar_uploaded_at' => null,
                    'e_wallet_type' => null,
                    'confirmed_by' => $user->id,
                    'confirmed_at' => now(),
                    'confirmation_notes' => $request->confirmation_notes,
                ]);

                // Hapus file bukti pembayaran yang ditolak
                if ($oldBuktiFile && Storage::disk('public')->exists($oldBuktiFile)) {
                    Storage::disk('public')->delete($oldBuktiFile);
                }

                $this->createPaymentNotification($kas, 'rejected');
                $message = 'Pembayaran ditolak. Masyarakat perlu mengirim ulang bukti pembayaran.';
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => $message]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => 'Gagal memproses konfirmasi: ' . $e->getMessage()]);
        }
    }

    /**
     * Show payment proof
     */
    public function showProof(Kas $kas)
    {
        $user = Auth::user();
        
        // Validasi akses
        if (!$this->canAccessKas($kas, $user)) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk melihat bukti pembayaran ini.');
        }

        if (!$kas->bukti_bayar_file) {
            return redirect()->back()->with('error', 'Bukti pembayaran tidak ditemukan.');
        }

        $kas->load(['penduduk', 'rt.rw', 'confirmedBy']);
        
        return view('kas.payment-proof', compact('kas'));
    }

    /**
     * Download payment proof
     */
    public function downloadProof(Kas $kas)
    {
        $user = Auth::user();
        
        // Validasi akses
        if (!$this->canAccessKas($kas, $user)) {
            abort(403, 'Anda tidak memiliki akses untuk mengunduh bukti pembayaran ini.');
        }

        if (!$kas->bukti_bayar_file || !Storage::disk('public')->exists($kas->bukti_bayar_file)) {
            abort(404, 'File bukti pembayaran tidak ditemukan.');
        }

        $filePath = Storage::disk('public')->path($kas->bukti_bayar_file);
        $fileName = 'bukti_pembayaran_kas_' . $kas->penduduk->nama_lengkap . '_minggu_' . $kas->minggu_ke . '_' . $kas->tahun . '.' . pathinfo($kas->bukti_bayar_file, PATHINFO_EXTENSION);

        return response()->download($filePath, $fileName);
    }

    /**
     * Payment success page
     */
    public function paymentSuccess(Kas $kas)
    {
        $user = Auth::user();
        
        // Validasi akses - hanya masyarakat pemilik kas
        if ($user->role !== 'masyarakat' || !$user->penduduk || $kas->penduduk_id !== $user->penduduk->id) {
            return redirect()->route('kas.index')->with('error', 'Akses ditolak.');
        }

        $kas->load(['penduduk', 'rt.rw']);
        
        return view('kas.payment-success', compact('kas'));
    }

    /**
     * List payments that need confirmation
     */
    public function paymentsList(Request $request)
    {
        $user = Auth::user();
        
        // Validasi akses
        if (!in_array($user->role, ['rt', 'rw', 'kades', 'admin'])) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $query = Kas::with(['penduduk', 'rt.rw'])
                    ->where('status', 'menunggu_konfirmasi');

        // Filter berdasarkan role
        switch ($user->role) {
            case 'rt':
                if ($user->penduduk && $user->penduduk->rtKetua) {
                    $query->where('rt_id', $user->penduduk->rtKetua->id);
                } else {
                    $query->whereRaw('1 = 0'); // No results
                }
                break;
            case 'rw':
                if ($user->penduduk && $user->penduduk->rwKetua) {
                    $rtIds = $user->penduduk->rwKetua->rts->pluck('id');
                    $query->whereIn('rt_id', $rtIds);
                } else {
                    $query->whereRaw('1 = 0');
                }
                break;
            // kades dan admin bisa lihat semua
        }

        // Filter berdasarkan request
        if ($request->filled('rt_id')) {
            $query->where('rt_id', $request->rt_id);
        }

        if ($request->filled('metode_bayar')) {
            $query->where('metode_bayar', $request->metode_bayar);
        }

        if ($request->filled('search')) {
            $query->whereHas('penduduk', function($q) use ($request) {
                $q->where('nama_lengkap', 'like', '%' . $request->search . '%')
                  ->orWhere('nik', 'like', '%' . $request->search . '%');
            });
        }

        $payments = $query->orderBy('bukti_bayar_uploaded_at', 'asc')->paginate(20);

        // Data untuk filter
        $rts = collect();
        if (in_array($user->role, ['rw', 'kades', 'admin'])) {
            if ($user->role === 'rw' && $user->penduduk && $user->penduduk->rwKetua) {
                $rts = $user->penduduk->rwKetua->rts;
            } else {
                $rts = \App\Models\Rt::all();
            }
        }

        return view('payments.list', compact('payments', 'rts'));
    }

    /**
     * Check if user can access specific kas
     */
    private function canAccessKas(Kas $kas, $user = null)
    {
        if (!$user) {
            $user = Auth::user();
        }

        switch ($user->role) {
            case 'admin':
            case 'kades':
                return true;
            case 'rw':
                if ($user->penduduk && $user->penduduk->rwKetua) {
                    return $kas->rt->rw_id === $user->penduduk->rwKetua->id;
                }
                return false;
            case 'rt':
                if ($user->penduduk && $user->penduduk->rtKetua) {
                    return $kas->rt_id === $user->penduduk->rtKetua->id;
                }
                return false;
            case 'masyarakat':
                if ($user->penduduk) {
                    return $kas->penduduk_id === $user->penduduk->id;
                }
                return false;
            default:
                return false;
        }
    }

    /**
     * Create notification for payment events
     */
    private function createPaymentNotification(Kas $kas, $type)
    {
        $kas->load(['penduduk', 'rt.rw']);
        
        switch ($type) {
            case 'submitted':
                // Notifikasi untuk RT
                $rtUser = \App\Models\User::whereHas('penduduk.rtKetua', function($q) use ($kas) {
                    $q->where('id', $kas->rt_id);
                })->first();

                if ($rtUser) {
                    Notifikasi::create([
                        'user_id' => $rtUser->id,
                        'judul' => 'Bukti Pembayaran Kas Baru',
                        'pesan' => "Bukti pembayaran kas dari {$kas->penduduk->nama_lengkap} untuk minggu ke-{$kas->minggu_ke} tahun {$kas->tahun} telah dikirim dan menunggu konfirmasi.",
                        'tipe' => 'info',
                        'kategori' => 'pembayaran_kas',
                        'data' => [
                            'kas_id' => $kas->id,
                            'penduduk_nama' => $kas->penduduk->nama_lengkap,
                            'minggu_ke' => $kas->minggu_ke,
                            'tahun' => $kas->tahun,
                            'jumlah' => $kas->total_bayar,
                            'metode_bayar' => $kas->metode_bayar_formatted,
                        ],
                    ]);
                }
                break;

            case 'approved':
                // Notifikasi untuk masyarakat
                $masyarakatUser = \App\Models\User::where('penduduk_id', $kas->penduduk_id)->first();
                
                if ($masyarakatUser) {
                    Notifikasi::create([
                        'user_id' => $masyarakatUser->id,
                        'judul' => 'Pembayaran Kas Dikonfirmasi',
                        'pesan' => "Pembayaran kas Anda untuk minggu ke-{$kas->minggu_ke} tahun {$kas->tahun} telah dikonfirmasi dan diterima.",
                        'tipe' => 'success',
                        'kategori' => 'pembayaran_kas',
                        'data' => [
                            'kas_id' => $kas->id,
                            'minggu_ke' => $kas->minggu_ke,
                            'tahun' => $kas->tahun,
                            'jumlah' => $kas->total_bayar,
                            'tanggal_bayar' => $kas->tanggal_bayar_formatted,
                        ],
                    ]);
                }
                break;

            case 'rejected':
                // Notifikasi untuk masyarakat
                $masyarakatUser = \App\Models\User::where('penduduk_id', $kas->penduduk_id)->first();
                
                if ($masyarakatUser) {
                    Notifikasi::create([
                        'user_id' => $masyarakatUser->id,
                        'judul' => 'Pembayaran Kas Ditolak',
                        'pesan' => "Pembayaran kas Anda untuk minggu ke-{$kas->minggu_ke} tahun {$kas->tahun} ditolak. Silakan kirim ulang bukti pembayaran yang valid.",
                        'tipe' => 'warning',
                        'kategori' => 'pembayaran_kas',
                        'data' => [
                            'kas_id' => $kas->id,
                            'minggu_ke' => $kas->minggu_ke,
                            'tahun' => $kas->tahun,
                            'jumlah' => $kas->total_bayar,
                            'alasan_penolakan' => $kas->confirmation_notes,
                        ],
                    ]);
                }
                break;
        }
    }
}
