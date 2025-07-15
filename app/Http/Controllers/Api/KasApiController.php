<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Kas;
use App\Models\Penduduk;
use Carbon\Carbon;

class KasApiController extends Controller
{
    /**
     * Get kas statistics for the authenticated user's role.
     */
    public function getStats(Request $request)
    {
        try {
            $user = Auth::user();
            $stats = [];

            if ($user->role === 'masyarakat') {
                $penduduk = Penduduk::where('user_id', $user->id)->first();
                if (!$penduduk) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Data penduduk tidak ditemukan untuk pengguna ini.'
                    ], 404);
                }

                $currentYear = Carbon::now()->year;
                $totalWeeksInYear = Carbon::createFromDate($currentYear, 12, 31)->weekOfYear;

                $kasQuery = Kas::where('penduduk_id', $penduduk->id) // Changed to $penduduk->id
                               ->where('tahun', $currentYear);

                $stats['userNik'] = $penduduk->nik;
                $stats['kasLunas'] = $kasQuery->clone()->where('status', 'lunas')->count();
                $stats['kasBelumBayar'] = $kasQuery->clone()->where('status', 'belum_bayar')->count();
                $stats['kasTerlambat'] = $kasQuery->clone()->where('status', 'belum_bayar')
                                                  ->where('tanggal_jatuh_tempo', '<', Carbon::now())->count();
                $stats['kasMenungguKonfirmasi'] = $kasQuery->clone()->where('status', 'menunggu_konfirmasi')->count();
                $stats['totalKasAnda'] = $kasQuery->clone()->where('status', 'lunas')->sum('jumlah');
                
                $paidWeeks = $kasQuery->clone()->whereIn('status', ['lunas', 'menunggu_konfirmasi'])->count();
                $stats['isYearCompleted'] = $paidWeeks >= $totalWeeksInYear;
                $stats['rtRw'] = ($penduduk->rt->no_rt ?? 'N/A') . ' / ' . ($penduduk->rt->rw->no_rw ?? 'N/A'); // Added rtRw

            } elseif ($user->role === 'rt') {
                $rt = $user->penduduk->rtKetua; // Corrected to use rtKetua relationship
                if (!$rt) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Data RT tidak ditemukan untuk pengguna ini.'
                    ], 404);
                }

                $stats['rtNumber'] = $rt->no_rt;
                $stats['totalKk'] = $rt->kks->count();
                $stats['totalPenduduk'] = $rt->penduduks->count();
                $stats['kasLunas'] = Kas::where('rt_id', $rt->id)->where('status', 'lunas')->count(); // Changed to $rt->id
                $stats['kasBelumBayar'] = Kas::where('rt_id', $rt->id)->whereIn('status', ['belum_bayar', 'terlambat', 'menunggu_konfirmasi'])->count(); // Changed to $rt->id

            } elseif ($user->role === 'rw') {
                $rw = $user->penduduk->rwKetua; // Corrected to use rwKetua relationship
                if (!$rw) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Data RW tidak ditemukan untuk pengguna ini.'
                    ], 404);
                }

                $rtIds = $rw->rts->pluck('id'); // Changed to 'id'
                $stats['rwNumber'] = $rw->no_rw;
                $stats['totalRts'] = $rtIds->count();
                $stats['totalPenduduk'] = Penduduk::whereIn('rt_id', $rtIds)->count();
                $stats['kasLunas'] = Kas::whereIn('rt_id', $rtIds)->where('status', 'lunas')->count();
                $stats['kasBelumBayar'] = Kas::whereIn('rt_id', $rtIds)->whereIn('status', ['belum_bayar', 'terlambat', 'menunggu_konfirmasi'])->count();

            } elseif ($user->role === 'kades') {
                $stats['totalRws'] = \App\Models\Rw::count();
                $stats['totalRts'] = \App\Models\Rt::count();
                $stats['totalPenduduk'] = Penduduk::count();
                $stats['totalKasTerkumpul'] = Kas::where('status', 'lunas')->sum('jumlah');

            } elseif ($user->role === 'admin') {
                $stats['totalUsers'] = \App\Models\User::count();
                $stats['totalRts'] = \App\Models\Rt::count();
                $stats['totalRws'] = \App\Models\Rw::count();
                $stats['totalPenduduk'] = Penduduk::count();
            }

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting dashboard stats', [
                'user_id' => Auth::id(),
                'role' => Auth::user()->role,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat statistik dashboard: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's kas bills.
     */
    public function getUserKas(Request $request, $userId)
    {
        try {
            $user = Auth::user();
            if ($user->id != $userId && !in_array($user->role, ['admin', 'kades', 'rw', 'rt'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses tidak diizinkan.'
                ], 403);
            }

            $penduduk = Penduduk::where('user_id', $userId)->first();
            if (!$penduduk) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data penduduk tidak ditemukan.'
                ], 404);
            }

            $kasList = Kas::where('penduduk_id', $penduduk->id) // Changed to $penduduk->id
                          ->orderBy('tahun', 'desc')
                          ->orderBy('minggu_ke', 'desc')
                          ->get()
                          ->map(function($kas) {
                              return [
                                  'id' => $kas->id, // Changed from kas_id to id
                                  'minggu_ke' => $kas->minggu_ke,
                                  'tahun' => $kas->tahun,
                                  'jumlah' => $kas->jumlah,
                                  'denda' => $kas->denda,
                                  'total_bayar' => $kas->jumlah + $kas->denda, // Calculate total_bayar
                                  'tanggal_jatuh_tempo_formatted' => $kas->tanggal_jatuh_tempo_formatted,
                                  'status' => $kas->status,
                                  'status_text' => $kas->status_text,
                                  'is_overdue' => $kas->is_overdue,
                                  'can_pay' => in_array($kas->status, ['belum_bayar', 'terlambat']),
                              ];
                          });

            return response()->json([
                'success' => true,
                'data' => $kasList
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting user kas', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data kas pengguna: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get recent payments (for RT/RW/Admin dashboards).
     */
    public function getRecentPayments(Request $request)
    {
        try {
            $user = Auth::user();
            $query = Kas::with(['penduduk', 'rt'])
                        ->whereIn('status', ['lunas', 'menunggu_konfirmasi'])
                        ->orderBy('tanggal_bayar', 'desc')
                        ->limit($request->get('limit', 5));

            if ($user->role === 'rt') {
                $query->where('rt_id', $user->penduduk->rtKetua->id); // Corrected to use rtKetua relationship
            } elseif ($user->role === 'rw') {
                $rtIds = $user->penduduk->rwKetua->rts->pluck('id'); // Corrected to use rwKetua relationship and 'id'
                $query->whereIn('rt_id', $rtIds);
            }

            $recentPayments = $query->get()->map(function($kas) {
                return [
                    'id' => $kas->id, // Changed from kas_id to id
                    'title' => 'Pembayaran Kas dari ' . ($kas->penduduk->nama_lengkap ?? 'N/A'), // Changed from nama to nama_lengkap
                    'description' => 'Minggu ke-' . $kas->minggu_ke . ' Tahun ' . $kas->tahun . ' - ' . $kas->status_text,
                    'amount' => $kas->jumlah + $kas->denda, // Calculate total_bayar
                    'timestamp' => $kas->tanggal_bayar ?? $kas->updated_at,
                    'status' => $kas->status,
                    'icon' => $kas->status === 'lunas' ? 'check-circle' : 'hourglass',
                    'color' => $kas->status === 'lunas' ? 'green' : 'yellow',
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $recentPayments
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting recent payments', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat pembayaran terbaru: ' . $e->getMessage()
            ], 500);
        }
    }
}
