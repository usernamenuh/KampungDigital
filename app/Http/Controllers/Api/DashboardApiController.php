<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Penduduk;
use App\Models\Rt;
use App\Models\Rw;
use App\Models\Kas;
use App\Models\Notifikasi;
use App\Models\Desa;
use App\Models\PaymentInfo;
use App\Models\Kk;

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

            switch ($user->role) {
                case 'admin':
                    $stats = $this->getAdminStats();
                    break;
                case 'kades':
                    $stats = $this->getKadesStats();
                    break;
                case 'rw':
                    $stats = $this->getRwStats($user);
                    break;
                case 'rt':
                    $stats = $this->getRtStats($user);
                    break;
                case 'masyarakat':
                    $stats = $this->getMasyarakatStats($user);
                    break;
                default:
                    $stats = $this->getBasicStats();
            }

            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => 'Data dashboard berhasil dimuat'
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting dashboard stats', [
                'user_id' => Auth::id(),
                'role' => Auth::user()->role ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat statistik dashboard: ' . $e->getMessage(),
                'data' => $this->getBasicStats()
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

            // Apply role-based filtering
            switch ($user->role) {
                case 'rt':
                    $rt = $this->getUserRt($user);
                    if ($rt) {
                        $query->where('rt_id', $rt->id);
                    }
                    break;
                case 'rw':
                    $rw = $this->getUserRw($user);
                    if ($rw) {
                        $rtIds = $rw->rts->pluck('id');
                        $query->whereIn('rt_id', $rtIds);
                    }
                    break;
                case 'masyarakat':
                    if ($user->penduduk) {
                        $query->where('penduduk_id', $user->penduduk->id);
                    }
                    break;
                // admin and kades can see all data
            }

            $monthlyData = $query->selectRaw('MONTH(tanggal_bayar) as month, SUM(jumlah) as total_amount')
                                 ->whereNotNull('tanggal_bayar')
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
                    'values' => $values,
                    'total' => array_sum($values)
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
            $limit = $request->get('limit', 20);
            $activities = [];

            if ($user->role === 'admin') {
                $activities = $this->getAllSystemActivities($limit);
            } else {
                $activities = $this->getRoleSpecificActivities($user, $limit);
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
                'message' => 'Gagal memuat aktivitas: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    /**
     * Get system monitoring data.
     */
    public function getSystemMonitoring(Request $request)
    {
        try {
            $monitoringData = [
                'cpu_usage' => rand(10, 80),
                'memory_usage' => rand(20, 90),
                'disk_usage' => rand(30, 95),
                'network_traffic' => rand(100, 1000),
                'serverLoad' => round(mt_rand() / mt_getrandmax() * (2.0 - 0.1) + 0.1, 1),
                'activeSessions' => User::where('last_activity', '>=', now()->subMinutes(5))->count(),
                'dbConnections' => rand(3, 15),
            ];

            return response()->json([
                'success' => true,
                'data' => $monitoringData
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting system monitoring data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat pemantauan sistem.',
                'data' => [
                    'serverLoad' => 0.5,
                    'memoryUsage' => 128,
                    'activeSessions' => 0,
                    'dbConnections' => 0
                ]
            ], 500);
        }
    }

    /**
     * Clear application cache.
     */
    public function clearCache(Request $request)
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');
            Cache::flush();
            
            return response()->json([
                'success' => true,
                'message' => 'Cache aplikasi berhasil dibersihkan.'
            ]);
        } catch (\Exception $e) {
            Log::error('Error clearing cache: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal membersihkan cache: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get system health status.
     */
    public function getSystemHealth(Request $request)
    {
        try {
            $checks = [
                'database' => $this->checkDatabase(),
                'cache' => $this->checkCache(),
                'storage' => $this->checkStorage(),
                'queue' => 'ok'
            ];
            
            $allHealthy = !in_array('error', array_values($checks));
            
            return response()->json([
                'success' => true,
                'data' => [
                    'status' => $allHealthy ? 'healthy' : 'degraded',
                    'checks' => $checks,
                    'timestamp' => now()->toISOString()
                ]
            ], $allHealthy ? 200 : 503);
            
        } catch (\Exception $e) {
            Log::error('Error getting system health: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'status' => 'unhealthy',
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString()
            ], 503);
        }
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

            $penduduk = $user->penduduk;
            if (!$penduduk) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data penduduk tidak ditemukan.'
                ], 404);
            }

            $alerts = [];
            $hasOverdue = false;

            $kasBills = Kas::where('penduduk_id', $penduduk->id)
                            ->whereIn('status', ['belum_bayar', 'terlambat', 'menunggu_konfirmasi'])
                            ->orderBy('tanggal_jatuh_tempo', 'asc')
                            ->get();

            foreach ($kasBills as $bill) {
                $type = 'info';
                $message = 'Tagihan kas minggu ke-' . $bill->minggu_ke . ' tahun ' . $bill->tahun . ' menunggu pembayaran.';
                $title = 'Tagihan Kas Mendatang';

                if ($bill->status === 'terlambat' || ($bill->status === 'belum_bayar' && $bill->tanggal_jatuh_tempo->isPast())) {
                    $type = 'error';
                    $message = 'Tagihan kas minggu ke-' . $bill->minggu_ke . ' tahun ' . $bill->tahun . ' sudah terlambat!';
                    $title = 'Tagihan Kas Terlambat';
                    $hasOverdue = true;
                } elseif ($bill->status === 'menunggu_konfirmasi') {
                    $type = 'warning';
                    $message = 'Pembayaran kas minggu ke-' . $bill->minggu_ke . ' tahun ' . $bill->tahun . ' sedang menunggu konfirmasi.';
                    $title = 'Pembayaran Menunggu Konfirmasi';
                }

                $alerts[] = [
                    'id' => $bill->id,
                    'title' => $title,
                    'message' => $message,
                    'type' => $type,
                    'total_bayar' => $bill->total_bayar,
                    'tanggal_jatuh_tempo' => $bill->tanggal_jatuh_tempo_formatted,
                    'payment_url' => route('kas.payment.form', $bill->id),
                    'is_overdue' => $bill->is_overdue,
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $alerts,
                'has_overdue' => $hasOverdue,
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting payment alerts: ' . $e->getMessage(), [
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

    /**
     * Get aggregated payment info for all RTs under the authenticated RW.
     */
    public function getAggregatedPaymentInfoForRw(Request $request)
    {
        try {
            $user = Auth::user();
            if ($user->role !== 'rw') {
                return response()->json(['success' => false, 'message' => 'Akses tidak diizinkan.'], 403);
            }

            $rw = $this->getUserRw($user);
            if (!$rw) {
                return response()->json(['success' => false, 'message' => 'Data RW tidak ditemukan untuk pengguna ini.'], 404);
            }

            $rtIds = $rw->rts->pluck('id');
            $paymentInfos = PaymentInfo::whereIn('rt_id', $rtIds)
                                        ->with('rt')
                                        ->get();

            $aggregatedData = $paymentInfos->map(function($info) {
                return [
                    'rt_number' => $info->rt->no_rt ?? 'N/A',
                    'bank_transfer' => $info->has_bank_transfer ? [
                        'bank_name' => $info->bank_name,
                        'account_number' => $info->bank_account_number,
                        'account_name' => $info->bank_account_name,
                    ] : null,
                    'e_wallet_list' => $info->has_e_wallet ? $info->e_wallet_list : null,
                    'qr_code' => $info->has_qr_code ? [
                        'path' => \Storage::url($info->qr_code_path),
                        'description' => $info->qr_code_description,
                    ] : null,
                    'payment_notes' => $info->payment_notes,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $aggregatedData
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting aggregated payment info for RW: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Failed to load aggregated payment info: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update User Activity
     */
    public function updateActivity(Request $request)
    {
        try {
            $user = Auth::user();
            if ($user) {
                $user->update([
                    'last_activity' => now()
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Aktivitas berhasil diperbarui'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui aktivitas'
            ], 500);
        }
    }

    /**
     * Get Admin Statistics - Fixed implementation
     */
    private function getAdminStats()
    {
        try {
            // Use safe queries with proper error handling
            $totalSaldoDesa = $this->safeSum('desas', 'saldo');
            $totalSaldoRw = $this->safeSum('rws', 'saldo');
            $totalSaldoRt = $this->safeSum('rts', 'saldo');

            return [
                'totalSaldoDesa' => $totalSaldoDesa,
                'totalSaldoRw' => $totalSaldoRw,
                'totalSaldoRt' => $totalSaldoRt,
                'totalSaldoSistem' => $totalSaldoDesa + $totalSaldoRw + $totalSaldoRt,
                'totalUsers' => User::count(),
                'totalDesa' => Desa::count(),
                'totalRws' => Rw::count(),
                'totalRts' => Rt::count(),
                'usersOnline' => User::where('last_activity', '>=', Carbon::now()->subMinutes(5))->count(),
                'activeUsers' => User::where('status', 'active')->count(),
                'inactiveUsers' => User::where('status', 'inactive')->count(),
                'totalPenduduk' => Penduduk::count(),
                'pendudukAktif' => Penduduk::where('status', 'aktif')->count(),
                'pendudukLakiLaki' => Penduduk::where('jenis_kelamin', 'L')->count(),
                'pendudukPerempuan' => Penduduk::where('jenis_kelamin', 'P')->count(),
                'totalKk' => Kk::count(),
                'totalKas' => Kas::count(),
                'totalKasTerkumpul' => Kas::where('status', 'lunas')->sum('jumlah') ?? 0,
                'totalKasBelumBayar' => Kas::whereIn('status', ['belum_bayar', 'terlambat', 'menunggu_konfirmasi'])->sum('jumlah') ?? 0,
                'jumlahKasBelumBayar' => Kas::whereIn('status', ['belum_bayar', 'terlambat', 'menunggu_konfirmasi'])->count(),
                'kasLunas' => Kas::where('status', 'lunas')->count(),
                'kasBelumBayar' => Kas::where('status', 'belum_bayar')->count(),
                'kasTerlambat' => Kas::where('status', 'terlambat')->count(),
                'kasMenungguKonfirmasi' => Kas::where('status', 'menunggu_konfirmasi')->count(),
                'kasHariIni' => Kas::whereDate('tanggal_bayar', today())->where('status', 'lunas')->count(),
                'kasBulanIni' => Kas::whereMonth('tanggal_bayar', now()->month)->where('status', 'lunas')->sum('jumlah') ?? 0,
                'totalNotifikasi' => Notifikasi::count(),
                'notifikasiUnread' => Notifikasi::where('dibaca', false)->count(),
                'notifikasiHariIni' => Notifikasi::whereDate('created_at', today())->count(),
                'systemHealth' => $this->getSystemHealthStatus(),
            ];
        } catch (\Exception $e) {
            Log::error('Error in getAdminStats', ['error' => $e->getMessage()]);
            return $this->getBasicStats();
        }
    }

    /**
     * Get Kades Statistics - Fixed implementation
     */
    private function getKadesStats()
    {
        try {
            return [
                'totalRws' => Rw::count(),
                'totalRts' => Rt::count(),
                'totalPenduduk' => Penduduk::count(),
                'totalKasTerkumpul' => Kas::where('status', 'lunas')->sum('jumlah') ?? 0,
                'pendudukAktif' => Penduduk::where('status', 'aktif')->count(),
                'pendudukLakiLaki' => Penduduk::where('jenis_kelamin', 'L')->count(),
                'pendudukPerempuan' => Penduduk::where('jenis_kelamin', 'P')->count(),
                'totalKk' => Kk::count(),
                'kasLunas' => Kas::where('status', 'lunas')->count(),
                'kasBelumBayar' => Kas::whereIn('status', ['belum_bayar', 'terlambat', 'menunggu_konfirmasi'])->count(),
            ];
        } catch (\Exception $e) {
            Log::error('Error in getKadesStats', ['error' => $e->getMessage()]);
            return $this->getBasicStats();
        }
    }

    /**
     * Get RW Statistics - Fixed implementation
     */
    private function getRwStats($user)
    {
        try {
            $rw = $this->getUserRw($user);
            if (!$rw) {
                Log::warning('RW not found for user', ['user_id' => $user->id]);
                return $this->getBasicStats();
            }

            $rtIds = $rw->rts->pluck('id');
            
            return [
                'rwId' => $rw->id,
                'rwNumber' => $rw->no_rw,
                'totalRts' => $rtIds->count(),
                'totalPenduduk' => Kk::whereIn('rt_id', $rtIds)->withCount('penduduks')->get()->sum('penduduks_count'),
                'kasLunas' => Kas::whereIn('rt_id', $rtIds)->where('status', 'lunas')->count(),
                'kasBelumBayar' => Kas::whereIn('rt_id', $rtIds)->whereIn('status', ['belum_bayar', 'terlambat', 'menunggu_konfirmasi'])->count(),
            ];
        } catch (\Exception $e) {
            Log::error('Error in getRwStats', ['error' => $e->getMessage(), 'user_id' => $user->id]);
            return $this->getBasicStats();
        }
    }

    /**
     * Get RT Statistics - Fixed implementation
     */
    private function getRtStats($user)
    {
        try {
            $rt = $this->getUserRt($user);
            if (!$rt) {
                Log::warning('RT not found for user', ['user_id' => $user->id]);
                return $this->getBasicStats();
            }

            return [
                'rtId' => $rt->id,
                'rtNumber' => $rt->no_rt,
                'totalKk' => Kk::where('rt_id', $rt->id)->count(),
                'totalPenduduk' => Kk::where('rt_id', $rt->id)->withCount('penduduks')->get()->sum('penduduks_count'),
                'kasLunas' => Kas::where('rt_id', $rt->id)->where('status', 'lunas')->count(),
                'kasBelumBayar' => Kas::where('rt_id', $rt->id)->whereIn('status', ['belum_bayar', 'terlambat', 'menunggu_konfirmasi'])->count(),
            ];
        } catch (\Exception $e) {
            Log::error('Error in getRtStats', ['error' => $e->getMessage(), 'user_id' => $user->id]);
            return $this->getBasicStats();
        }
    }

    /**
     * Get Masyarakat Statistics - Fixed for proper kas display
     */
    private function getMasyarakatStats($user)
    {
        try {
            $penduduk = $user->penduduk;
            if (!$penduduk) {
                Log::warning('Penduduk not found for user', ['user_id' => $user->id]);
                return $this->getBasicStats();
            }

            $currentYear = Carbon::now()->year;
            $totalWeeksInYear = Carbon::createFromDate($currentYear, 12, 31)->weekOfYear;

            $kasQuery = Kas::where('penduduk_id', $penduduk->id)
                           ->where('tahun', $currentYear);

            $kasLunas = $kasQuery->clone()->where('status', 'lunas')->count();
            $kasBelumBayar = $kasQuery->clone()->where('status', 'belum_bayar')->count();
            $kasTerlambat = $kasQuery->clone()->where('status', 'belum_bayar')
                                    ->where('tanggal_jatuh_tempo', '<', Carbon::now())->count();
            $kasMenungguKonfirmasi = $kasQuery->clone()->where('status', 'menunggu_konfirmasi')->count();
            $totalKasAnda = $kasQuery->clone()->where('status', 'lunas')->sum('jumlah') ?? 0;
            
            $paidWeeks = $kasQuery->clone()->whereIn('status', ['lunas', 'menunggu_konfirmasi'])->count();
            $isYearCompleted = $paidWeeks >= $totalWeeksInYear;

            // Get RT/RW info safely
            $rtRw = 'N/A';
            if ($penduduk->kk && $penduduk->kk->rt) {
                $rt = $penduduk->kk->rt;
                $rw = $rt->rw;
                $rtRw = 'RT ' . ($rt->no_rt ?? 'N/A') . ' / RW ' . ($rw->no_rw ?? 'N/A');
            }

            return [
                'userNik' => $penduduk->nik,
                'rtRw' => $rtRw,
                'kasLunas' => $kasLunas,
                'kasBelumBayar' => $kasBelumBayar,
                'kasTerlambat' => $kasTerlambat,
                'kasMenungguKonfirmasi' => $kasMenungguKonfirmasi,
                'totalKasAnda' => $totalKasAnda,
                'isYearCompleted' => $isYearCompleted,
                'notifikasiUnread' => Notifikasi::where('user_id', $user->id)->where('dibaca', false)->count(),
            ];
        } catch (\Exception $e) {
            Log::error('Error in getMasyarakatStats', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => $user->id
            ]);
            return $this->getBasicStats();
        }
    }

    /**
     * Get Basic Statistics (fallback)
     */
    private function getBasicStats()
    {
        return [
            'totalSaldoDesa' => 0,
            'totalSaldoRw' => 0,
            'totalSaldoRt' => 0,
            'totalSaldoSistem' => 0,
            'totalUsers' => 0,
            'totalDesa' => 0,
            'totalRws' => 0,
            'totalRts' => 0,
            'usersOnline' => 0,
            'activeUsers' => 0,
            'inactiveUsers' => 0,
            'totalPenduduk' => 0,
            'pendudukAktif' => 0,
            'pendudukLakiLaki' => 0,
            'pendudukPerempuan' => 0,
            'totalKk' => 0,
            'totalKas' => 0,
            'totalKasTerkumpul' => 0,
            'totalKasBelumBayar' => 0,
            'jumlahKasBelumBayar' => 0,
            'kasLunas' => 0,
            'kasBelumBayar' => 0,
            'kasTerlambat' => 0,
            'kasMenungguKonfirmasi' => 0,
            'kasHariIni' => 0,
            'kasBulanIni' => 0,
            'totalNotifikasi' => 0,
            'notifikasiUnread' => 0,
            'notifikasiHariIni' => 0,
            'systemHealth' => ['status' => 'unknown', 'checks' => [], 'timestamp' => now()->toISOString()],
            'userNik' => 'N/A',
            'rtRw' => 'N/A',
            'isYearCompleted' => false,
            'rtId' => null,
            'rtNumber' => 'N/A',
            'rwId' => null,
            'rwNumber' => 'N/A',
        ];
    }

    /**
     * Get All System Activities
     */
    private function getAllSystemActivities($limit)
    {
        $activities = [];

        try {
            $recentPayments = Kas::with(['penduduk', 'rt.rw'])
                ->where('status', 'lunas')
                ->whereNotNull('tanggal_bayar')
                ->orderBy('tanggal_bayar', 'desc')
                ->limit($limit / 2)
                ->get();

            foreach ($recentPayments as $payment) {
                $activities[] = [
                    'id' => 'kas_' . $payment->id,
                    'type' => 'kas_payment',
                    'title' => 'Pembayaran Kas',
                    'description' => "{$payment->penduduk->nama_lengkap} membayar kas minggu ke-{$payment->minggu_ke}",
                    'amount' => $payment->jumlah,
                    'user' => $payment->penduduk->nama_lengkap,
                    'location' => "RT {$payment->rt->no_rt} RW {$payment->rt->rw->no_rw}",
                    'timestamp' => $payment->tanggal_bayar,
                    'icon' => 'credit-card',
                    'color' => 'green'
                ];
            }

            $recentUsers = User::with('penduduk')
                ->orderBy('created_at', 'desc')
                ->limit($limit / 2)
                ->get();

            foreach ($recentUsers as $newUser) {
                $activities[] = [
                    'id' => 'user_' . $newUser->id,
                    'type' => 'user_registration',
                    'title' => 'Registrasi User Baru',
                    'description' => "User baru {$newUser->name} ({$newUser->role}) telah mendaftar",
                    'user' => $newUser->name,
                    'role' => $newUser->role,
                    'timestamp' => $newUser->created_at,
                    'icon' => 'user-plus',
                    'color' => 'blue'
                ];
            }

            usort($activities, function($a, $b) {
                return $b['timestamp'] <=> $a['timestamp'];
            });

            return array_slice($activities, 0, $limit);
        } catch (\Exception $e) {
            Log::error('Error getting system activities', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Get Role Specific Activities
     */
    private function getRoleSpecificActivities($user, $limit)
    {
        $activities = [];

        try {
            if ($user->role === 'masyarakat') {
                $activities = Notifikasi::where('user_id', $user->id)
                                        ->orderBy('created_at', 'desc')
                                        ->limit($limit)
                                        ->get();
            } elseif ($user->role === 'rt') {
                $rt = $this->getUserRt($user);
                if ($rt) {
                    $activities = Notifikasi::whereHas('kas', function($q) use ($rt) {
                                            $q->where('rt_id', $rt->id);
                                        })
                                    ->orWhere('user_id', $user->id)
                                    ->with('user', 'kas.penduduk', 'kas.rt.rw')
                                    ->orderBy('created_at', 'desc')
                                    ->limit($limit)
                                    ->get();
                }
            } elseif ($user->role === 'rw') {
                $rw = $this->getUserRw($user);
                if ($rw) {
                    $rtIds = $rw->rts->pluck('id');
                    $activities = Notifikasi::whereHas('kas', function($q) use ($rtIds) {
                                            $q->whereIn('rt_id', $rtIds);
                                        })
                                    ->orWhere('user_id', $user->id)
                                    ->with('user', 'kas.penduduk', 'kas.rt.rw')
                                    ->orderBy('created_at', 'desc')
                                    ->limit($limit)
                                    ->get();
                }
            } elseif ($user->role === 'kades') {
                $activities = Notifikasi::with('user', 'kas.penduduk', 'kas.rt.rw')
                                        ->orderBy('created_at', 'desc')
                                        ->limit($limit)
                                        ->get();
            }

            $formattedActivities = $activities->map(function($activity) {
                $data = json_decode($activity->data, true);
                $title = $activity->judul;
                $description = $activity->pesan;
                $icon = 'activity';
                $color = 'blue';
                $amount = null;
                $location = null;
                $userName = $activity->user->name ?? 'Sistem';

                if ($activity->kategori === 'pembayaran' && isset($data['kas_id'])) {
                    $kas = $activity->kas;
                    if ($kas) {
                        $title = 'Pembayaran Kas dari ' . ($kas->penduduk->nama_lengkap ?? 'Warga');
                        $description = 'Minggu ke-' . $kas->minggu_ke . ' Tahun ' . $kas->tahun . ' - ' . $kas->status_text;
                        $amount = $kas->total_bayar;
                        $location = 'RT ' . ($kas->rt->no_rt ?? 'N/A') . '/RW ' . ($kas->rt->rw->no_rw ?? 'N/A');
                        if ($kas->status === 'lunas') {
                            $icon = 'check-circle';
                            $color = 'green';
                        } elseif ($kas->status === 'menunggu_konfirmasi') {
                            $icon = 'hourglass';
                            $color = 'yellow';
                        } elseif ($kas->status === 'belum_bayar' && $kas->confirmed_at) {
                            $icon = 'x-circle';
                            $color = 'red';
                        }
                    }
                } elseif ($activity->kategori === 'sistem') {
                    $icon = 'settings';
                    $color = 'purple';
                } elseif ($activity->kategori === 'pengguna') {
                    $icon = 'user';
                    $color = 'blue';
                }

                return [
                    'id' => $activity->id,
                    'title' => $title,
                    'description' => $description,
                    'icon' => $icon,
                    'color' => $color,
                    'timestamp' => $activity->created_at,
                    'amount' => $amount,
                    'location' => $location,
                    'user' => $userName,
                ];
            });

            return $formattedActivities;
        } catch (\Exception $e) {
            Log::error('Error getting role specific activities', ['error' => $e->getMessage()]);
            return [];
        }
    }

    // Helper methods
    private function safeSum($table, $column, $conditions = [])
    {
        try {
            $query = DB::table($table);
            foreach ($conditions as $key => $value) {
                $query->where($key, $value);
            }
            return $query->sum($column) ?? 0;
        } catch (\Exception $e) {
            Log::error("Error in safeSum for table {$table}.{$column}", ['error' => $e->getMessage()]);
            return 0;
        }
    }

    private function safeCount($table, $conditions = [])
    {
        try {
            $query = DB::table($table);
            foreach ($conditions as $key => $value) {
                $query->where($key, $value);
            }
            return $query->count();
        } catch (\Exception $e) {
            Log::error("Error in safeCount for table {$table}", ['error' => $e->getMessage()]);
            return 0;
        }
    }

    private function getUserRw($user)
    {
        try {
            // Method 1: Check if user has penduduk relationship and is RW ketua
            if ($user->penduduk && $user->penduduk->rwKetua) {
                return $user->penduduk->rwKetua;
            }
            
            // Method 2: Check if user is directly assigned as RW ketua
            $rw = Rw::where('ketua_rw_id', $user->penduduk->id ?? null)->first();
            if ($rw) {
                return $rw;
            }

            // Method 3: Check by user_id if there's a direct relationship
            $rw = Rw::whereHas('ketua', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })->first();
            
            return $rw;
        } catch (\Exception $e) {
            Log::error('Error getting user RW', ['error' => $e->getMessage(), 'user_id' => $user->id]);
            return null;
        }
    }

    private function getUserRt($user)
    {
        try {
            // Method 1: Check if user has penduduk relationship and is RT ketua
            if ($user->penduduk && $user->penduduk->rtKetua) {
                return $user->penduduk->rtKetua;
            }
            
            // Method 2: Check if user is directly assigned as RT ketua
            $rt = Rt::where('ketua_rt_id', $user->penduduk->id ?? null)->first();
            if ($rt) {
                return $rt;
            }

            // Method 3: Check by user_id if there's a direct relationship
            $rt = Rt::whereHas('ketua', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })->first();
            
            return $rt;
        } catch (\Exception $e) {
            Log::error('Error getting user RT', ['error' => $e->getMessage(), 'user_id' => $user->id]);
            return null;
        }
    }

    private function getSystemHealthStatus()
    {
        $checks = [
            'database' => $this->checkDatabase(),
            'cache' => $this->checkCache(),
            'storage' => $this->checkStorage(),
            'queue' => 'ok'
        ];

        $allHealthy = !in_array('error', array_values($checks));

        return [
            'status' => $allHealthy ? 'healthy' : 'degraded',
            'checks' => $checks,
            'timestamp' => now()->toISOString()
        ];
    }

    private function checkDatabase()
    {
        try {
            DB::connection()->getPdo();
            return 'ok';
        } catch (\Exception $e) {
            return 'error';
        }
    }

    private function checkCache()
    {
        try {
            Cache::put('health_check', 'ok', 60);
            return Cache::get('health_check') === 'ok' ? 'ok' : 'error';
        } catch (\Exception $e) {
            return 'error';
        }
    }

    private function checkStorage()
    {
        try {
            return is_writable(storage_path()) ? 'ok' : 'error';
        } catch (\Exception $e) {
            return 'error';
        }
    }
}
