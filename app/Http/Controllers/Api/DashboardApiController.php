<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Penduduk;
use App\Models\Rt;
use App\Models\Rw;
use App\Models\Kas;
use App\Models\Notifikasi;

class DashboardApiController extends Controller
{
    /**
     * Get general statistics for the authenticated user's dashboard based on their role.
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

                $kasQuery = Kas::where('penduduk_id', $penduduk->penduduk_id)
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

            } elseif ($user->role === 'rt') {
                $rt = $user->rt;
                if (!$rt) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Data RT tidak ditemukan untuk pengguna ini.'
                    ], 404);
                }

                $stats['rtNumber'] = $rt->no_rt;
                $stats['totalKk'] = $rt->kks->count();
                $stats['totalPenduduk'] = $rt->penduduks->count();
                $stats['kasLunas'] = Kas::where('rt_id', $rt->rt_id)->where('status', 'lunas')->count();
                $stats['kasBelumBayar'] = Kas::where('rt_id', $rt->rt_id)->whereIn('status', ['belum_bayar', 'terlambat', 'menunggu_konfirmasi'])->count();

            } elseif ($user->role === 'rw') {
                $rw = $user->rw;
                if (!$rw) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Data RW tidak ditemukan untuk pengguna ini.'
                    ], 404);
                }

                $rtIds = $rw->rts->pluck('rt_id');
                $stats['rwNumber'] = $rw->no_rw;
                $stats['totalRts'] = $rtIds->count();
                $stats['totalPenduduk'] = Penduduk::whereIn('rt_id', $rtIds)->count();
                $stats['kasLunas'] = Kas::whereIn('rt_id', $rtIds)->where('status', 'lunas')->count();
                $stats['kasBelumBayar'] = Kas::whereIn('rt_id', $rtIds)->whereIn('status', ['belum_bayar', 'terlambat', 'menunggu_konfirmasi'])->count();

            } elseif ($user->role === 'kades') {
                $stats['totalRws'] = Rw::count();
                $stats['totalRts'] = Rt::count();
                $stats['totalPenduduk'] = Penduduk::count();
                $stats['totalKasTerkumpul'] = Kas::where('status', 'lunas')->sum('jumlah');

            } elseif ($user->role === 'admin') {
                $stats['totalUsers'] = User::count();
                $stats['totalRts'] = Rt::count();
                $stats['totalRws'] = Rw::count();
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
     * Get monthly kas data for charts.
     */
    public function getMonthlyKasData(Request $request)
    {
        try {
            $user = Auth::user();
            $currentYear = Carbon::now()->year;
            $query = Kas::where('tahun', $currentYear)
                        ->where('status', 'lunas');

            if ($user->role === 'rt') {
                $query->where('rt_id', $user->rt->rt_id);
            } elseif ($user->role === 'rw') {
                $rtIds = $user->rw->rts->pluck('rt_id');
                $query->whereIn('rt_id', $rtIds);
            }

            $monthlyData = $query->selectRaw('MONTH(tanggal_bayar) as month, SUM(jumlah) as total_amount')
                                 ->groupBy('month')
                                 ->orderBy('month')
                                 ->get();

            $labels = [];
            $values = [];
            for ($i = 1; $i <= 12; $i++) {
                $monthName = Carbon::create()->month($i)->translatedFormat('F');
                $labels[] = $monthName;
                $values[] = $monthlyData->firstWhere('month', $i)->total_amount ?? 0;
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'labels' => $labels,
                    'values' => $values
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting monthly kas data', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data kas bulanan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get recent activities/notifications.
     */
    public function getActivities(Request $request)
    {
        try {
            $user = Auth::user();
            $activities = [];

            if ($user->role === 'masyarakat') {
                $activities = Notifikasi::where('user_id', $user->id)
                                        ->orderBy('created_at', 'desc')
                                        ->limit($request->get('limit', 5))
                                        ->get()
                                        ->map(function($notif) {
                                            return [
                                                'id' => $notif->notifikasi_id,
                                                'title' => $notif->judul,
                                                'description' => $notif->pesan,
                                                'timestamp' => $notif->created_at,
                                                'icon' => 'bell', // Default icon
                                                'color' => $notif->is_read ? 'gray' : 'blue', // Example color logic
                                            ];
                                        });
            } elseif (in_array($user->role, ['rt', 'rw', 'kades', 'admin'])) {
                // For RT/RW/Kades/Admin, show recent payment confirmations or other system activities
                $query = Kas::with(['penduduk', 'rt'])
                            ->whereIn('status', ['lunas', 'menunggu_konfirmasi'])
                            ->orderBy('updated_at', 'desc')
                            ->limit($request->get('limit', 5));

                if ($user->role === 'rt') {
                    $query->where('rt_id', $user->rt->rt_id);
                } elseif ($user->role === 'rw') {
                    $rtIds = $user->rw->rts->pluck('rt_id');
                    $query->whereIn('rt_id', $rtIds);
                }

                $activities = $query->get()->map(function($kas) {
                    $title = $kas->status === 'lunas' ? 'Pembayaran Dikonfirmasi' : 'Pembayaran Menunggu Konfirmasi';
                    $description = 'Kas Minggu ke-' . $kas->minggu_ke . ' Tahun ' . $kas->tahun . ' dari ' . ($kas->penduduk->nama ?? 'N/A');
                    $icon = $kas->status === 'lunas' ? 'check-circle' : 'hourglass';
                    $color = $kas->status === 'lunas' ? 'green' : 'yellow';
                    
                    return [
                        'id' => $kas->kas_id,
                        'title' => $title,
                        'description' => $description,
                        'timestamp' => $kas->updated_at,
                        'icon' => $icon,
                        'color' => $color,
                    ];
                });
            }

            return response()->json([
                'success' => true,
                'data' => $activities
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting activities', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat aktivitas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get system monitoring data (for admin).
     */
    public function getSystemMonitoring()
    {
        // This is a placeholder. In a real app, you'd fetch actual system metrics.
        return response()->json([
            'success' => true,
            'data' => [
                'cpu_usage' => rand(10, 80),
                'memory_usage' => rand(20, 90),
                'disk_usage' => rand(30, 70),
                'network_traffic' => rand(100, 1000),
            ]
        ]);
    }

    /**
     * Clear application cache.
     */
    public function clearCache()
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');
            
            return response()->json([
                'success' => true,
                'message' => 'Cache aplikasi berhasil dibersihkan.'
            ]);
        } catch (\Exception $e) {
            Log::error('Error clearing cache', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal membersihkan cache: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get system health status.
     */
    public function getSystemHealth()
    {
        try {
            $checks = [
                'database' => \Illuminate\Support\Facades\DB::connection()->getPdo() ? 'ok' : 'error',
                'cache' => cache()->put('health_check', 'ok', 60) ? 'ok' : 'error',
                'storage' => is_writable(storage_path()) ? 'ok' : 'error'
            ];
            
            $allHealthy = !in_array('error', array_values($checks));
            
            return response()->json([
                'success' => true,
                'status' => $allHealthy ? 'healthy' : 'degraded',
                'checks' => $checks,
                'timestamp' => now()->toISOString()
            ], $allHealthy ? 200 : 503);
            
        } catch (\Exception $e) {
            Log::error('Error getting system health', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'status' => 'unhealthy',
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString()
            ], 500);
        }
    }

    /**
     * Update activity (placeholder for custom activity logging).
     */
    public function updateActivity(Request $request)
    {
        // This is a placeholder for a custom activity logging endpoint
        // In a real application, you might log specific user actions here.
        Log::info('Activity logged', [
            'user_id' => Auth::id(),
            'action' => $request->input('action'),
            'details' => $request->input('details'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Activity logged successfully.'
        ]);
    }

    /**
     * Get payment alerts for masyarakat dashboard.
     */
    public function getPaymentAlerts(Request $request)
    {
        try {
            $user = Auth::user();
            if ($user->role !== 'masyarakat') {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses tidak diizinkan.'
                ], 403);
            }

            $penduduk = Penduduk::where('user_id', $user->id)->first();
            if (!$penduduk) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data penduduk tidak ditemukan.'
                ], 404);
            }

            $alerts = [];
            $hasOverdue = false;

            $kasBills = Kas::where('penduduk_id', $penduduk->penduduk_id)
                           ->whereIn('status', ['belum_bayar', 'terlambat'])
                           ->orderBy('tanggal_jatuh_tempo', 'asc')
                           ->get();

            foreach ($kasBills as $bill) {
                $alert = [
                    'id' => $bill->kas_id,
                    'kas_id' => $bill->kas_id,
                    'total_bayar' => $bill->total_bayar,
                    'tanggal_jatuh_tempo' => $bill->tanggal_jatuh_tempo_formatted,
                    'payment_url' => route('kas.payment.form', $bill->kas_id),
                    'is_overdue' => $bill->is_overdue,
                ];

                if ($bill->is_overdue) {
                    $alert['type'] = 'error';
                    $alert['title'] = 'Pembayaran Kas Terlambat!';
                    $alert['message'] = 'Tagihan kas minggu ke-' . $bill->minggu_ke . ' tahun ' . $bill->tahun . ' sudah jatuh tempo.';
                    $hasOverdue = true;
                } elseif ($bill->tanggal_jatuh_tempo->diffInDays(Carbon::now()) <= 7) {
                    $alert['type'] = 'warning';
                    $alert['title'] = 'Pembayaran Kas Segera Jatuh Tempo!';
                    $alert['message'] = 'Tagihan kas minggu ke-' . $bill->minggu_ke . ' tahun ' . $bill->tahun . ' akan jatuh tempo dalam ' . $bill->tanggal_jatuh_tempo->diffInDays(Carbon::now()) . ' hari.';
                } else {
                    $alert['type'] = 'info';
                    $alert['title'] = 'Tagihan Kas Mendatang';
                    $alert['message'] = 'Anda memiliki tagihan kas untuk minggu ke-' . $bill->minggu_ke . ' tahun ' . $bill->tahun . '.';
                }
                $alerts[] = $alert;
            }

            return response()->json([
                'success' => true,
                'data' => $alerts,
                'has_overdue' => $hasOverdue,
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting payment alerts', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat peringatan pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }
}

