<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kas;
use App\Models\Penduduk;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class KasApiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Get kas statistics for dashboard
     */
    public function getStats()
    {
        try {
            $user = Auth::user();
            $query = Kas::query();

            // Filter berdasarkan role
            switch ($user->role) {
                case 'admin':
                case 'kades':
                    // Admin dan Kades bisa lihat semua kas
                    break;
                case 'rw':
                    // RW hanya bisa lihat kas di RW mereka
                    if ($user->penduduk && $user->penduduk->kk && $user->penduduk->kk->rt) {
                        $rwId = $user->penduduk->kk->rt->rw_id;
                        $query->whereHas('rt', function($q) use ($rwId) {
                            $q->where('rw_id', $rwId);
                        });
                    }
                    break;
                case 'rt':
                    // RT hanya bisa lihat kas di RT mereka
                    if ($user->penduduk && $user->penduduk->kk) {
                        $rtId = $user->penduduk->kk->rt_id;
                        $query->where('rt_id', $rtId);
                    }
                    break;
                case 'masyarakat':
                    // Masyarakat hanya bisa lihat kas mereka sendiri
                    if ($user->penduduk) {
                        $query->where('penduduk_id', $user->penduduk->id);
                    }
                    break;
            }

            $stats = [
                'kasLunas' => $query->clone()->where('status', 'lunas')->count(),
                'kasBelumBayar' => $query->clone()->where('status', 'belum_bayar')->count(),
                'kasTerlambat' => $query->clone()->where('status', 'terlambat')->count(),
                'totalKasAnda' => $query->clone()->where('status', 'lunas')->sum('jumlah'),
                'total_kas_belum_bayar' => $query->clone()->whereIn('status', ['belum_bayar', 'terlambat'])->sum('jumlah'),
                'jumlah_kas_belum_bayar' => $query->clone()->whereIn('status', ['belum_bayar', 'terlambat'])->count(),
                'total_kas_lunas' => $query->clone()->where('status', 'lunas')->count(),
                'kas_terlambat' => $query->clone()->where('tanggal_jatuh_tempo', '<', now())->where('status', '!=', 'lunas')->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting kas stats', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil statistik kas'
            ], 500);
        }
    }

    /**
     * Get kas list for dashboard
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            $query = Kas::with(['penduduk', 'rt.rw']);

            // Filter berdasarkan role
            switch ($user->role) {
                case 'admin':
                case 'kades':
                    // Admin dan Kades bisa lihat semua kas
                    break;
                case 'rw':
                    // RW hanya bisa lihat kas di RW mereka
                    if ($user->penduduk && $user->penduduk->kk && $user->penduduk->kk->rt) {
                        $rwId = $user->penduduk->kk->rt->rw_id;
                        $query->whereHas('rt', function($q) use ($rwId) {
                            $q->where('rw_id', $rwId);
                        });
                    }
                    break;
                case 'rt':
                    // RT hanya bisa lihat kas di RT mereka
                    if ($user->penduduk && $user->penduduk->kk) {
                        $rtId = $user->penduduk->kk->rt_id;
                        $query->where('rt_id', $rtId);
                    }
                    break;
                case 'masyarakat':
                    // Masyarakat hanya bisa lihat kas mereka sendiri
                    if ($user->penduduk) {
                        $query->where('penduduk_id', $user->penduduk->id);
                    }
                    break;
            }

            // Apply filters
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('rt_id')) {
                $query->where('rt_id', $request->rt_id);
            }

            if ($request->filled('minggu_ke')) {
                $query->where('minggu_ke', $request->minggu_ke);
            }

            if ($request->filled('tahun')) {
                $query->where('tahun', $request->tahun);
            }

            // Pagination
            $page = $request->get('page', 1);
            $limit = $request->get('limit', 10);
            
            $kas = $query->orderBy('created_at', 'desc')
                         ->paginate($limit, ['*'], 'page', $page);

            // Format data
            $formattedKas = $kas->map(function ($item) {
                return [
                    'id' => $item->id,
                    'penduduk_id' => $item->penduduk_id,
                    'penduduk_nama' => $item->penduduk ? $item->penduduk->nama_lengkap : 'N/A',
                    'rt_id' => $item->rt_id,
                    'minggu_ke' => $item->minggu_ke,
                    'tahun' => $item->tahun,
                    'jumlah' => $item->jumlah,
                    'status' => $item->status,
                    'tanggal_jatuh_tempo' => $item->tanggal_jatuh_tempo->toDateString(),
                    'tanggal_bayar' => $item->tanggal_bayar ? $item->tanggal_bayar->toDateString() : null,
                    'metode_bayar' => $item->metode_bayar,
                    'keterangan' => $item->keterangan,
                    'is_overdue' => $item->tanggal_jatuh_tempo < now() && $item->status !== 'lunas',
                    'created_at' => $item->created_at->toISOString(),
                    'updated_at' => $item->updated_at->toISOString(),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $formattedKas,
                'pagination' => [
                    'current_page' => $kas->currentPage(),
                    'last_page' => $kas->lastPage(),
                    'per_page' => $kas->perPage(),
                    'total' => $kas->total(),
                    'has_more' => $kas->hasMorePages()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting kas list', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil daftar kas'
            ], 500);
        }
    }

    /**
     * Process payment for kas
     */
    public function pay(Request $request, $kasId)
    {
        try {
            $user = Auth::user();
            
            $request->validate([
                'metode_bayar' => 'required|string|in:tunai,transfer,digital,e_wallet',
                'keterangan' => 'nullable|string|max:500'
            ]);

            $kas = Kas::findOrFail($kasId);

            // Validasi akses
            if ($user->role === 'masyarakat' && $kas->penduduk_id !== $user->penduduk->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke kas ini'
                ], 403);
            }

            // Cek apakah kas sudah dibayar
            if ($kas->status === 'lunas') {
                return response()->json([
                    'success' => false,
                    'message' => 'Kas ini sudah dibayar'
                ], 400);
            }

            DB::beginTransaction();

            // Update kas
            $kas->update([
                'status' => 'lunas',
                'tanggal_bayar' => now(),
                'metode_bayar' => $request->metode_bayar,
                'bukti_bayar' => $request->keterangan,
                'dibayar_oleh' => $user->id,
            ]);

            // Kirim notifikasi konfirmasi pembayaran
            if ($kas->penduduk->user) {
                Notifikasi::create([
                    'user_id' => $kas->penduduk->user->id,
                    'judul' => 'Pembayaran Kas Berhasil',
                    'pesan' => "Pembayaran kas minggu ke-{$kas->minggu_ke} sebesar Rp " . number_format($kas->jumlah, 0, ',', '.') . " telah berhasil dikonfirmasi.",
                    'tipe' => 'success',
                    'kategori' => 'kas',
                    'data' => json_encode([
                        'kas_id' => $kas->id,
                        'jumlah' => $kas->jumlah,
                        'metode_bayar' => $request->metode_bayar,
                        'tanggal_bayar' => now()->toDateString(),
                    ])
                ]);
            }

            // Kirim notifikasi ke admin/RT/RW
            $adminUsers = \App\Models\User::whereIn('role', ['admin', 'kades', 'rw', 'rt'])
                ->where('status', 'aktif')
                ->get();

            foreach ($adminUsers as $adminUser) {
                Notifikasi::create([
                    'user_id' => $adminUser->id,
                    'judul' => 'Kas Dibayar',
                    'pesan' => "Kas minggu ke-{$kas->minggu_ke} telah dibayar oleh {$kas->penduduk->nama_lengkap}. Jumlah: Rp " . number_format($kas->jumlah, 0, ',', '.'),
                    'tipe' => 'info',
                    'kategori' => 'kas',
                    'data' => json_encode([
                        'kas_id' => $kas->id,
                        'penduduk_nama' => $kas->penduduk->nama_lengkap,
                        'jumlah' => $kas->jumlah,
                        'metode_bayar' => $request->metode_bayar,
                    ])
                ]);
            }

            DB::commit();

            Log::info('Kas payment processed', [
                'kas_id' => $kas->id,
                'user_id' => $user->id,
                'metode_bayar' => $request->metode_bayar,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran kas berhasil dikonfirmasi',
                'data' => [
                    'kas_id' => $kas->id,
                    'status' => $kas->status,
                    'tanggal_bayar' => $kas->tanggal_bayar->toDateString(),
                    'metode_bayar' => $kas->metode_bayar
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error processing kas payment', [
                'kas_id' => $kasId,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get recent payments for dashboard
     */
    public function getRecentPayments(Request $request)
    {
        try {
            $user = Auth::user();
            $query = Kas::with(['penduduk', 'rt.rw'])
                        ->where('status', 'lunas')
                        ->whereNotNull('tanggal_bayar');

            // Filter berdasarkan role
            switch ($user->role) {
                case 'admin':
                case 'kades':
                    // Admin dan Kades bisa lihat semua kas
                    break;
                case 'rw':
                    // RW hanya bisa lihat kas di RW mereka
                    if ($user->penduduk && $user->penduduk->kk && $user->penduduk->kk->rt) {
                        $rwId = $user->penduduk->kk->rt->rw_id;
                        $query->whereHas('rt', function($q) use ($rwId) {
                            $q->where('rw_id', $rwId);
                        });
                    }
                    break;
                case 'rt':
                    // RT hanya bisa lihat kas di RT mereka
                    if ($user->penduduk && $user->penduduk->kk) {
                        $rtId = $user->penduduk->kk->rt_id;
                        $query->where('rt_id', $rtId);
                    }
                    break;
                case 'masyarakat':
                    // Masyarakat hanya bisa lihat kas mereka sendiri
                    if ($user->penduduk) {
                        $query->where('penduduk_id', $user->penduduk->id);
                    }
                    break;
            }

            $limit = $request->get('limit', 5);
            $payments = $query->orderBy('tanggal_bayar', 'desc')
                             ->limit($limit)
                             ->get();

            $formattedPayments = $payments->map(function ($payment) {
                return [
                    'id' => $payment->id,
                    'penduduk_nama' => $payment->penduduk ? $payment->penduduk->nama_lengkap : 'N/A',
                    'minggu_ke' => $payment->minggu_ke,
                    'tahun' => $payment->tahun,
                    'jumlah' => $payment->jumlah,
                    'tanggal_bayar' => $payment->tanggal_bayar->toDateString(),
                    'metode_bayar' => $payment->metode_bayar,
                    'rt_nama' => $payment->rt ? "RT {$payment->rt->no_rt}" : 'N/A',
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $formattedPayments
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting recent payments', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil riwayat pembayaran'
            ], 500);
        }
    }

    /**
     * Send weekly notification (RT only)
     */
    public function sendWeeklyNotification(Request $request)
    {
        try {
            $user = Auth::user();
            
            if ($user->role !== 'rt') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya RT yang dapat mengirim notifikasi mingguan'
                ], 403);
            }

            $request->validate([
                'jumlah' => 'required|numeric|min:1000',
                'jatuh_tempo' => 'required|date|after:today',
                'keterangan' => 'nullable|string|max:500'
            ]);

            // Mock implementation - dalam implementasi nyata, ini akan membuat kas dan mengirim notifikasi
            $mingguKe = now()->weekOfYear;
            $tahun = now()->year;

            return response()->json([
                'success' => true,
                'message' => "Berhasil mengirim notifikasi kas minggu ke-{$mingguKe} kepada seluruh warga RT",
                'data' => [
                    'minggu_ke' => $mingguKe,
                    'tahun' => $tahun,
                    'jumlah' => $request->jumlah,
                    'jatuh_tempo' => $request->jatuh_tempo,
                    'keterangan' => $request->keterangan
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error sending weekly notification', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim notifikasi: ' . $e->getMessage()
            ], 500);
        }
    }
}
