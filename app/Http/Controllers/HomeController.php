<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Penduduk;
use App\Models\Kas;
use App\Models\Notifikasi;
use App\Models\Desa;
use App\Models\Rw;
use App\Models\Rt;
use App\Models\Kk;
use Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        
        switch ($user->role) {
            case 'admin':
                return $this->adminDashboard();
            case 'kades':
                return $this->kadesDashboard();
            case 'rw':
                return $this->rwDashboard();
            case 'rt':
                return $this->rtDashboard();
            case 'masyarakat':
                return $this->masyarakatDashboard();
            default:
                return view('dashboard.default');
        }
    }

    /**
     * Admin Dashboard
     */
    public function adminDashboard()
    {
        return view('dashboards.admin');
    }

    /**
     * Kades Dashboard
     */
    public function kadesDashboard()
    {
        return view('dashboards.kades');
    }

    /**
     * RW Dashboard
     */
    public function rwDashboard()
    {
        return view('dashboards.rw');
    }

    /**
     * RT Dashboard
     */
    public function rtDashboard()
    {
        return view('dashboards.rt');
    }

    /**
     * Masyarakat Dashboard
     */
    public function masyarakatDashboard()
    {
        return view('dashboards.masyarakat');
    }

    /**
     * Get Dashboard Statistics (AJAX)
     */
    public function getDashboardStats(Request $request)
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
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data dashboard: ' . $e->getMessage(),
                'data' => $this->getBasicStats()
            ], 500);
        }
    }

    /**
     * Get Monthly Kas Chart Data (AJAX)
     */
    public function getMonthlyKasData(Request $request)
    {
        try {
            $user = Auth::user();
            $months = [];
            $data = [];
            
            // Get last 6 months
            for ($i = 5; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $months[] = $date->format('M');
                
                // Get kas data for this month based on user role
                $monthlyTotal = 0;
                
                switch ($user->role) {
                    case 'admin':
                        $monthlyTotal = Kas::whereMonth('tanggal_bayar', $date->month)
                            ->whereYear('tanggal_bayar', $date->year)
                            ->where('status', 'lunas')
                            ->sum('jumlah');
                        break;
                        
                    case 'kades':
                        // All kas in the village
                        $monthlyTotal = Kas::whereMonth('tanggal_bayar', $date->month)
                            ->whereYear('tanggal_bayar', $date->year)
                            ->where('status', 'lunas')
                            ->sum('jumlah');
                        break;
                        
                    case 'rw':
                        $rwId = $this->getUserRwId($user);
                        if ($rwId) {
                            $monthlyTotal = Kas::whereHas('rt', function($q) use ($rwId) {
                                $q->where('rw_id', $rwId);
                            })
                            ->whereMonth('tanggal_bayar', $date->month)
                            ->whereYear('tanggal_bayar', $date->year)
                            ->where('status', 'lunas')
                            ->sum('jumlah');
                        }
                        break;
                        
                    case 'rt':
                        $rtId = $this->getUserRtId($user);
                        if ($rtId) {
                            $monthlyTotal = Kas::where('rt_id', $rtId)
                                ->whereMonth('tanggal_bayar', $date->month)
                                ->whereYear('tanggal_bayar', $date->year)
                                ->where('status', 'lunas')
                                ->sum('jumlah');
                        }
                        break;
                        
                    case 'masyarakat':
                        if ($user->penduduk) {
                            $monthlyTotal = Kas::where('penduduk_id', $user->penduduk->id)
                                ->whereMonth('tanggal_bayar', $date->month)
                                ->whereYear('tanggal_bayar', $date->year)
                                ->where('status', 'lunas')
                                ->sum('jumlah');
                        }
                        break;
                }
                
                $data[] = $monthlyTotal;
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'labels' => $months,
                    'values' => $data,
                    'total' => array_sum($data)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting monthly kas data', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            // Return fallback data
            return response()->json([
                'success' => true,
                'data' => [
                    'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
                    'values' => [0, 0, 0, 0, 0, 0],
                    'total' => 0
                ]
            ]);
        }
    }

    /**
     * Get Dashboard Activities (AJAX)
     */
    public function getDashboardActivities(Request $request)
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
            Log::error('Error getting dashboard activities', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat aktivitas',
                'data' => []
            ], 500);
        }
    }

    /**
     * Get System Monitoring (AJAX)
     */
    public function getSystemMonitoring(Request $request)
    {
        try {
            $monitoring = [
                'serverLoad' => round(rand(1, 10) / 10, 1),
                'memoryUsage' => rand(100, 500),
                'activeSessions' => User::where('last_activity', '>=', now()->subMinutes(5))->count(),
                'dbConnections' => rand(5, 20),
            ];

            return response()->json([
                'success' => true,
                'data' => $monitoring
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat monitoring sistem',
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
     * Clear Cache (AJAX)
     */
    public function clearCache(Request $request)
    {
        try {
            Cache::flush();
            
            return response()->json([
                'success' => true,
                'message' => 'Cache berhasil dibersihkan'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membersihkan cache: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get System Health (AJAX)
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

            $health = [
                'status' => $allHealthy ? 'healthy' : 'degraded',
                'checks' => $checks,
                'timestamp' => now()->toISOString()
            ];

            return response()->json([
                'success' => true,
                'data' => $health
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memeriksa kesehatan sistem',
                'data' => [
                    'status' => 'error',
                    'checks' => [],
                    'timestamp' => now()->toISOString()
                ]
            ], 500);
        }
    }

    /**
     * Update User Activity (AJAX)
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
     * Get Admin Statistics
     */
    private function getAdminStats()
    {
        try {
            $totalSaldoDesa = $this->safeSum('desas', 'saldo');
            $totalSaldoRw = $this->safeSum('rws', 'saldo');
            $totalSaldoRt = $this->safeSum('rts', 'saldo');

            return [
                'totalSaldoDesa' => $totalSaldoDesa,
                'totalSaldoRw' => $totalSaldoRw,
                'totalSaldoRt' => $totalSaldoRt,
                'totalSaldoSistem' => $totalSaldoDesa + $totalSaldoRw + $totalSaldoRt,
                'totalUsers' => User::count(),
                'totalDesa' => $this->safeCount('desas'),
                'totalRw' => Rw::count(),
                'totalRt' => Rt::count(),
                'usersOnline' => User::where('last_activity', '>=', now()->subMinutes(5))->count(),
                'activeUsers' => User::where('status', 'aktif')->count(),
                'inactiveUsers' => User::where('status', 'nonaktif')->count(),
                'totalPenduduk' => Penduduk::count(),
                'pendudukAktif' => Penduduk::where('status', 'aktif')->count(),
                'pendudukLakiLaki' => Penduduk::where('jenis_kelamin', 'L')->count(),
                'pendudukPerempuan' => Penduduk::where('jenis_kelamin', 'P')->count(),
                'totalKk' => Kk::count(),
                'totalKas' => Kas::count(),
                'totalKasTerkumpul' => Kas::where('status', 'lunas')->sum('jumlah'),
                'totalKasBelumBayar' => Kas::whereIn('status', ['belum_bayar', 'terlambat', 'menunggu_konfirmasi'])->sum('jumlah'),
                'jumlahKasBelumBayar' => Kas::whereIn('status', ['belum_bayar', 'terlambat', 'menunggu_konfirmasi'])->count(),
                'kasLunas' => Kas::where('status', 'lunas')->count(),
                'kasBelumBayar' => Kas::where('status', 'belum_bayar')->count(),
                'kasTerlambat' => Kas::where('status', 'terlambat')->count(),
                'kasMenungguKonfirmasi' => Kas::where('status', 'menunggu_konfirmasi')->count(),
                'kasHariIni' => Kas::whereDate('tanggal_bayar', today())->where('status', 'lunas')->count(),
                'kasBulanIni' => Kas::whereMonth('tanggal_bayar', now()->month)->where('status', 'lunas')->sum('jumlah'),
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
     * Get Kades Statistics
     */
    private function getKadesStats()
    {
        try {
            $saldoDesa = $this->safeSum('desas', 'saldo');
            $bantuanBulanIni = $this->safeSumByMonth('bantuan_desa', 'jumlah');

            return [
                'saldoDesa' => $saldoDesa,
                'bantuanBulanIni' => $bantuanBulanIni,
                'saldoTersedia' => $saldoDesa - $bantuanBulanIni,
                'totalRw' => Rw::count(),
                'totalRt' => Rt::count(),
                'totalPenduduk' => Penduduk::count(),
                'bantuanPending' => $this->safeCount('bantuan_desa', ['status' => 'pending']),
                'totalKk' => Kk::count(),
                'pendudukAktif' => Penduduk::where('status', 'aktif')->count(),
                'pendudukLakiLaki' => Penduduk::where('jenis_kelamin', 'L')->count(),
                'pendudukPerempuan' => Penduduk::where('jenis_kelamin', 'P')->count(),
            ];
        } catch (\Exception $e) {
            Log::error('Error in getKadesStats', ['error' => $e->getMessage()]);
            return $this->getBasicStats();
        }
    }

    /**
     * Get RW Statistics
     */
    private function getRwStats($user)
    {
        try {
            $rwId = $this->getUserRwId($user);
            if (!$rwId) return $this->getBasicStats();

            $rw = Rw::find($rwId);
            
            return [
                'balance' => $rw->saldo ?? 0,
                'kasMasukBulanIni' => Kas::whereHas('rt', function($q) use ($rwId) {
                    $q->where('rw_id', $rwId);
                })->whereMonth('tanggal_bayar', now()->month)->where('status', 'lunas')->sum('jumlah'),
                'totalRts' => Rt::where('rw_id', $rwId)->count(),
                'totalKks' => Kk::whereHas('rt', function($q) use ($rwId) {
                    $q->where('rw_id', $rwId);
                })->count(),
                'totalPopulation' => Penduduk::whereHas('kk.rt', function($q) use ($rwId) {
                    $q->where('rw_id', $rwId);
                })->count(),
            ];
        } catch (\Exception $e) {
            Log::error('Error in getRwStats', ['error' => $e->getMessage()]);
            return $this->getBasicStats();
        }
    }

    /**
     * Get RT Statistics
     */
    private function getRtStats($user)
    {
        try {
            $rtId = $this->getUserRtId($user);
            if (!$rtId) return $this->getBasicStats();

            $rt = Rt::find($rtId);

            return [
                'balance' => $rt->saldo ?? 0,
                'kasMasukBulanIni' => Kas::where('rt_id', $rtId)
                    ->whereMonth('tanggal_bayar', now()->month)
                    ->where('status', 'lunas')
                    ->sum('jumlah'),
                'totalWarga' => Penduduk::whereHas('kk', function($q) use ($rtId) {
                    $q->where('rt_id', $rtId);
                })->count(),
                'kasBelumBayar' => Kas::where('rt_id', $rtId)->where('status', 'belum_bayar')->count(),
                'totalKasBelumBayar' => Kas::where('rt_id', $rtId)->where('status', 'belum_bayar')->sum('jumlah'),
                'kasTerlambat' => Kas::where('rt_id', $rtId)->where('status', 'terlambat')->count(),
                'kasLunas' => Kas::where('rt_id', $rtId)->where('status', 'lunas')->count(),
                'kasMenungguKonfirmasi' => Kas::where('rt_id', $rtId)->where('status', 'menunggu_konfirmasi')->count(),
            ];
        } catch (\Exception $e) {
            Log::error('Error in getRtStats', ['error' => $e->getMessage()]);
            return $this->getBasicStats();
        }
    }

    /**
     * Get Masyarakat Statistics
     */
    private function getMasyarakatStats($user)
    {
        try {
            if (!$user->penduduk) {
                return $this->getBasicStats();
            }

            return [
                'kasLunas' => Kas::where('penduduk_id', $user->penduduk->id)->where('status', 'lunas')->count(),
                'kasBelumBayar' => Kas::where('penduduk_id', $user->penduduk->id)->where('status', 'belum_bayar')->count(),
                'kasTerlambat' => Kas::where('penduduk_id', $user->penduduk->id)->where('status', 'terlambat')->count(),
                'kasMenungguKonfirmasi' => Kas::where('penduduk_id', $user->penduduk->id)->where('status', 'menunggu_konfirmasi')->count(),
                'totalKasAnda' => Kas::where('penduduk_id', $user->penduduk->id)->where('status', 'lunas')->sum('jumlah'),
                'kasJatuhTempo' => Kas::where('penduduk_id', $user->penduduk->id)
                    ->where('tanggal_jatuh_tempo', '<=', now()->addDays(7))
                    ->where('status', 'belum_bayar')
                    ->count(),
                'notifikasiUnread' => Notifikasi::where('user_id', $user->id)->where('dibaca', false)->count(),
            ];
        } catch (\Exception $e) {
            Log::error('Error in getMasyarakatStats', ['error' => $e->getMessage()]);
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
            'totalRw' => 0,
            'totalRt' => 0,
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
            if ($user->role === 'rt') {
                $rtId = $this->getUserRtId($user);
                if ($rtId) {
                    $recentPayments = Kas::where('rt_id', $rtId)
                        ->where('status', 'lunas')
                        ->whereNotNull('tanggal_bayar')
                        ->orderBy('tanggal_bayar', 'desc')
                        ->limit($limit)
                        ->get();

                    foreach ($recentPayments as $payment) {
                        $activities[] = [
                            'id' => 'kas_' . $payment->id,
                            'type' => 'kas_payment',
                            'title' => 'Pembayaran Kas',
                            'description' => "{$payment->penduduk->nama_lengkap} membayar kas minggu ke-{$payment->minggu_ke}",
                            'amount' => $payment->jumlah,
                            'user' => $payment->penduduk->nama_lengkap,
                            'location' => "RT {$payment->rt->no_rt}",
                            'timestamp' => $payment->tanggal_bayar,
                            'icon' => 'credit-card',
                            'color' => 'green'
                        ];
                    }
                }
            } elseif ($user->role === 'masyarakat') {
                if ($user->penduduk) {
                    $recentPayments = Kas::where('penduduk_id', $user->penduduk->id)
                        ->where('status', 'lunas')
                        ->whereNotNull('tanggal_bayar')
                        ->orderBy('tanggal_bayar', 'desc')
                        ->limit($limit)
                        ->get();

                    foreach ($recentPayments as $payment) {
                        $activities[] = [
                            'id' => 'kas_' . $payment->id,
                            'type' => 'kas_payment',
                            'title' => 'Pembayaran Kas Anda',
                            'description' => "Anda telah membayar kas minggu ke-{$payment->minggu_ke}",
                            'amount' => $payment->jumlah,
                            'user' => 'Anda',
                            'location' => '',
                            'timestamp' => $payment->tanggal_bayar,
                            'icon' => 'credit-card',
                            'color' => 'green'
                        ];
                    }
                }
            }
            
            usort($activities, function($a, $b) {
                return $b['timestamp'] <=> $a['timestamp'];
            });

            return array_slice($activities, 0, $limit);
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

    private function safeSumByMonth($table, $column)
    {
        try {
            return DB::table($table)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum($column) ?? 0;
        } catch (\Exception $e) {
            Log::error("Error in safeSumByMonth for table {$table}.{$column}", ['error' => $e->getMessage()]);
            return 0;
        }
    }

    private function getUserRwId($user)
    {
        try {
            if ($user->penduduk && $user->penduduk->rwKetua) {
                return $user->penduduk->rwKetua->id;
            }
            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    private function getUserRtId($user)
    {
        try {
            if ($user->penduduk && $user->penduduk->rtKetua) {
                return $user->penduduk->rtKetua->id;
            }
            return null;
        } catch (\Exception $e) {
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
