<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Kas;
use App\Models\Penduduk;
use App\Models\Notifikasi;
use App\Models\Rt;
use App\Models\Rw;
use App\Models\Desa;
use App\Models\Kk;
use App\Models\PaymentInfo; // Added PaymentInfo
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DashboardApiController extends Controller
{
    public function __construct()
    {
        // Menggunakan middleware 'auth:sanctum' untuk API
        $this->middleware('auth:sanctum'); 
    }

    /**
     * Test API endpoint
     */
    public function test()
    {
        return response()->json([
            'success' => true,
            'message' => 'Dashboard API is working',
            'timestamp' => now()->toISOString(),
            'user' => Auth::user()->name ?? 'Guest', // Handle case where user might not be authenticated (though middleware should prevent)
            'authenticated' => Auth::check(),
            'user_id' => Auth::id()
        ]);
    }

    /**
     * Get comprehensive dashboard statistics for all roles
     */
    public function getStats()
    {
        try {
            $user = Auth::user();
            
            Log::info('Getting dashboard stats for user', [
                'user_id' => $user->id,
                'role' => $user->role,
                'name' => $user->name
            ]);
            
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
                'data' => $stats
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
                'message' => 'Gagal mengambil statistik dashboard: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Admin Dashboard Stats
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
                'totalMasyarakat' => User::where('role', 'masyarakat')->count(),
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
            Log::error('Error in getAdminStats', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return $this->getBasicStats();
        }
    }

    /**
     * Kades Dashboard Stats
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
                'pengajuanBantuan' => $this->getPengajuanBantuan()
            ];
        } catch (\Exception $e) {
            Log::error('Error in getKadesStats', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return $this->getBasicStats();
        }
    }

    /**
     * RW Dashboard Stats
     */
    private function getRwStats($user)
    {
        try {
            $rwId = $this->getUserRwId($user);

            if (!$rwId) {
                Log::warning('RW ID not found for user: ' . $user->id);
                return $this->getBasicStats();
            }

            $rw = Rw::find($rwId);
            
            return [
                'balance' => $rw->saldo ?? 0,
                'kasMasukBulanIni' => Kas::whereHas('rt', function($q) use ($rwId) {
                    $q->where('rw_id', $rwId);
                })->whereMonth('tanggal_bayar', now()->month)->where('status', 'lunas')->sum('jumlah'),
                'bantuanDiterima' => $this->safeSum('bantuan_desa', 'jumlah', [
                    'rw_id' => $rwId,
                    'status' => 'approved'
                ]),
                'totalRts' => Rt::where('rw_id', $rwId)->count(),
                'totalKks' => Kk::whereHas('rt', function($q) use ($rwId) {
                    $q->where('rw_id', $rwId);
                })->count(),
                'totalPopulation' => Penduduk::whereHas('kk.rt', function($q) use ($rwId) {
                    $q->where('rw_id', $rwId);
                })->count(),
                'bantuanPending' => $this->safeCount('bantuan_desa', [
                    'rw_id' => $rwId,
                    'status' => 'pending'
                ]),
                'rtData' => $this->getRtDataForRw($rwId)
            ];
        } catch (\Exception $e) {
            Log::error('Error in getRwStats', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return $this->getBasicStats();
        }
    }

    /**
     * RT Dashboard Stats
     */
    private function getRtStats($user)
    {
        try {
            $rtId = $this->getUserRtId($user);

            if (!$rtId) {
                Log::warning('RT ID not found for user: ' . $user->id);
                return $this->getBasicStats();
            }

            $rt = Rt::find($rtId);

            return [
                'balance' => $rt->saldo ?? 0,
                'kasMasukBulanIni' => Kas::where('rt_id', $rtId)
                    ->whereMonth('tanggal_bayar', now()->month)
                    ->where('status', 'lunas')
                    ->sum('jumlah'),
                'iuranMingguan' => 10000,
                'totalWarga' => Penduduk::whereHas('kk', function($q) use ($rtId) {
                    $q->where('rt_id', $rtId);
                })->count(),
                'kasBelumBayar' => Kas::where('rt_id', $rtId)->where('status', 'belum_bayar')->count(),
                'totalKasBelumBayar' => Kas::where('rt_id', $rtId)->where('status', 'belum_bayar')->sum('jumlah'),
                'kasTerlambat' => Kas::where('rt_id', $rtId)->where('status', 'terlambat')->count(),
                'kasLunas' => Kas::where('rt_id', $rtId)->where('status', 'lunas')->count(),
                'kasMenungguKonfirmasi' => Kas::where('rt_id', $rtId)->where('status', 'menunggu_konfirmasi')->count(),
                'daftarWarga' => $this->getDaftarWargaRt($rtId)
            ];
        } catch (\Exception $e) {
            Log::error('Error in getRtStats', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return $this->getBasicStats();
        }
    }

    /**
     * Masyarakat Dashboard Stats
     */
    private function getMasyarakatStats($user)
    {
        try {
            if (!$user->penduduk) {
                Log::warning('Penduduk data not found for user: ' . $user->id);
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
            Log::error('Error in getMasyarakatStats', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return $this->getBasicStats();
        }
    }

    /**
     * Basic stats fallback
     */
    private function getBasicStats()
    {
        return [
            'totalUsers' => 0,
            'totalPenduduk' => 0,
            'totalKas' => 0,
            'kasLunas' => 0,
            'balance' => 0,
            'bantuanBulanIni' => 0,
            'saldoTersedia' => 0,
            'totalRw' => 0,
            'totalRt' => 0,
            'bantuanPending' => 0,
            'pengajuanBantuan' => [],
            'daftarWarga' => [],
            'rtData' => [],
            'kasMenungguKonfirmasi' => 0,
            'totalSaldoDesa' => 0,
            'totalSaldoRw' => 0,
            'totalSaldoRt' => 0,
            'totalSaldoSistem' => 0,
            'usersOnline' => 0,
            'activeUsers' => 0,
            'inactiveUsers' => 0,
            'pendudukAktif' => 0,
            'pendudukLakiLaki' => 0,
            'pendudukPerempuan' => 0,
            'totalKk' => 0,
            'totalKasTerkumpul' => 0,
            'totalKasBelumBayar' => 0,
            'jumlahKasBelumBayar' => 0,
            'kasBelumBayar' => 0,
            'kasTerlambat' => 0,
            'kasHariIni' => 0,
            'kasBulanIni' => 0,
            'totalNotifikasi' => 0,
            'notifikasiUnread' => 0,
            'notifikasiHariIni' => 0,
            'systemHealth' => ['status' => 'unknown', 'checks' => [], 'timestamp' => now()->toISOString()],
        ];
    }

    // Helper methods
    private function safeSum($table, $column, $conditions = [])
    {
        try {
            $query = DB::table($table);
            foreach ($conditions as $key => $value) {
                $query->where($key, $value);
            }
            $result = $query->sum($column);
            Log::debug("safeSum query for {$table}.{$column}: " . $query->toSql(), $query->getBindings());
            return $result ?? 0;
        } catch (\Exception $e) {
            Log::error("Error in safeSum for table {$table}.{$column}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'conditions' => $conditions
            ]);
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
            $result = $query->count();
            Log::debug("safeCount query for {$table}: " . $query->toSql(), $query->getBindings());
            return $result;
        } catch (\Exception $e) {
            Log::error("Error in safeCount for table {$table}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'conditions' => $conditions
            ]);
            return 0;
        }
    }

    private function safeCountByDate($table, $dateColumn, $date)
    {
        try {
            $query = DB::table($table)->whereDate($dateColumn, $date);
            $result = $query->count();
            Log::debug("safeCountByDate query for {$table}.{$dateColumn}: " . $query->toSql(), $query->getBindings());
            return $result;
        } catch (\Exception $e) {
            Log::error("Error in safeCountByDate for table {$table}.{$dateColumn}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'date' => $date
            ]);
            return 0;
        }
    }

    private function safeSumByMonth($table, $column)
    {
        try {
            $query = DB::table($table)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year);
            $result = $query->sum($column);
            Log::debug("safeSumByMonth query for {$table}.{$column}: " . $query->toSql(), $query->getBindings());
            return $result ?? 0;
        } catch (\Exception $e) {
            Log::error("Error in safeSumByMonth for table {$table}.{$column}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
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
            Log::error('Error getting user RW ID', ['user_id' => $user->id, 'error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
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
            Log::error('Error getting user RT ID', ['user_id' => $user->id, 'error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return null;
        }
    }

    private function getPengajuanBantuan()
    {
        try {
            $query = DB::table('bantuan_desa as bd')
                ->join('rws as r', 'bd.rw_id', '=', 'r.id')
                ->where('bd.status', 'pending')
                ->select('bd.id', 'r.nama_rw as rw', 'bd.jumlah', 'bd.created_at as tanggal')
                ->orderBy('bd.created_at', 'desc')
                ->limit(5);
            
            Log::debug("getPengajuanBantuan query: " . $query->toSql(), $query->getBindings());
            
            return $query->get()
                ->map(function($item) {
                    return [
                        'id' => $item->id,
                        'rw' => $item->rw,
                        'jumlah' => $item->jumlah,
                        'tanggal' => Carbon::parse($item->tanggal)->format('d M Y')
                    ];
                });
        } catch (\Exception $e) {
            Log::error('Error getting pengajuan bantuan', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return [];
        }
    }

    private function getRtDataForRw($rwId)
    {
        try {
            $rts = Rt::where('rw_id', $rwId)
                ->withCount(['kks as total_kk', 'kks as total_penduduk' => function($q) {
                    $q->join('penduduks', 'kks.id', '=', 'penduduks.kk_id');
                }])
                ->get();
            
            return $rts->map(function($rt) {
                    return [
                        'nama' => $rt->nama_rt,
                        'total_penduduk' => $rt->total_penduduk ?? 0
                    ];
                });
        } catch (\Exception $e) {
            Log::error('Error getting RT data for RW', ['rw_id' => $rwId, 'error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return [];
        }
    }

    private function getDaftarWargaRt($rtId)
    {
        try {
            $penduduks = Penduduk::whereHas('kk', function($q) use ($rtId) {
                $q->where('rt_id', $rtId);
            })
            ->with(['kk', 'kas' => function($q) {
                $q->latest()->first();
            }])
            ->limit(10)
            ->get();
            
            return $penduduks->map(function($penduduk) {
                $kasStatus = 'lunas';
                $statusText = 'Lunas';
                
                if ($penduduk->kas && $penduduk->kas->isNotEmpty()) {
                    $latestKas = $penduduk->kas->first();
                    $kasStatus = $latestKas->status;
                    $statusText = match($latestKas->status) {
                        'lunas' => 'Lunas',
                        'belum_bayar' => 'Belum Bayar',
                        'terlambat' => 'Terlambat',
                        'menunggu_konfirmasi' => 'Menunggu Konfirmasi',
                        default => 'Unknown'
                    };
                }
                
                return [
                    'id' => $penduduk->id,
                    'nama' => $penduduk->nama_lengkap,
                    'alamat' => $penduduk->kk->alamat ?? 'N/A',
                    'status' => $kasStatus,
                    'statusText' => $statusText
                ];
            });
        } catch (\Exception $e) {
            Log::error('Error getting daftar warga RT', ['rt_id' => $rtId, 'error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return [];
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
            Log::error('Database health check failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return 'error';
        }
    }

    private function checkCache()
    {
        try {
            Cache::put('health_check', 'ok', 60);
            return Cache::get('health_check') === 'ok' ? 'ok' : 'error';
        } catch (\Exception $e) {
            Log::error('Cache health check failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return 'error';
        }
    }

    private function checkStorage()
    {
        try {
            return is_writable(storage_path()) ? 'ok' : 'error';
        } catch (\Exception $e) {
            Log::error('Storage health check failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return 'error';
        }
    }

    /**
     * Get activities for dashboard
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
                'message' => 'Gagal mengambil aktivitas'
            ], 500);
        }
    }

    private function getAllSystemActivities($limit)
    {
        $activities = [];

        try {
            $recentPayments = Kas::with(['penduduk', 'rt.rw'])
                ->where('status', 'lunas')
                ->whereNotNull('tanggal_bayar')
                ->orderBy('tanggal_bayar', 'desc')
                ->limit($limit / 2) // Reduced limit to make space for other types if needed
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
                ->limit($limit / 2) // Reduced limit
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

            // Combine and sort activities by timestamp
            usort($activities, function($a, $b) {
                return $b['timestamp'] <=> $a['timestamp'];
            });

            return array_slice($activities, 0, $limit);
        } catch (\Exception $e) {
            Log::error('Error getting system activities', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return [];
        }
    }

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
            } elseif ($user->role === 'rw') {
                $rwId = $this->getUserRwId($user);
                if ($rwId) {
                    $rtIds = \App\Models\Rt::where('rw_id', $rwId)->pluck('id');
                    $recentPayments = Kas::whereIn('rt_id', $rtIds)
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
                            'description' => "{$payment->penduduk->nama_lengkap} membayar kas minggu ke-{$payment->minggu_ke} di RT {$payment->rt->no_rt}",
                            'amount' => $payment->jumlah,
                            'user' => $payment->penduduk->nama_lengkap,
                            'location' => "RT {$payment->rt->no_rt} RW {$payment->rt->rw->no_rw}",
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
            Log::error('Error getting role specific activities', ['user_id' => $user->id, 'role' => $user->role, 'error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return [];
        }
    }

    /**
     * Get online status for header
     */
    public function getOnlineStatus()
    {
        try {
            $onlineUsersCount = User::where('last_activity', '>=', now()->subMinutes(5))->count();
            return response()->json([
                'success' => true,
                'data' => [
                    'online_users' => $onlineUsersCount
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting online status', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil status online'
            ], 500);
        }
    }

    /**
     * Update user last activity
     */
    public function updateUserActivity(Request $request)
    {
        try {
            $user = Auth::user();
            if ($user) {
                $user->update(['last_activity' => now()]);
                return response()->json([
                    'success' => true,
                    'message' => 'Aktivitas terakhir diperbarui'
                ]);
            }
            return response()->json([
                'success' => false,
                'message' => 'User tidak terautentikasi'
            ], 401);
        } catch (\Exception $e) {
            Log::error('Error updating user activity', ['user_id' => Auth::id(), 'error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui aktivitas'
            ], 500);
        }
    }

    /**
     * Perform a global search across multiple models.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function globalSearch(Request $request)
    {
        $query = $request->input('q');
        $results = [];

        if (empty($query)) {
            return response()->json([
                'success' => true,
                'data' => []
            ]);
        }

        try {
            // Search Users
            $users = User::where('name', 'like', '%' . $query . '%')
                         ->orWhere('email', 'like', '%' . $query . '%')
                         ->limit(5)
                         ->get(['id', 'name', 'email', 'role']);
            foreach ($users as $user) {
                $results[] = [
                    'type' => 'user',
                    'id' => $user->id,
                    'title' => $user->name,
                    'description' => $user->email . ' (' . $user->role . ')',
                    'link' => '/admin/users/' . $user->id, // Example link
                ];
            }

            // Search Penduduk (by NIK or nama_lengkap)
            $penduduks = Penduduk::where('nik', 'like', '%' . $query . '%')
                                 ->orWhere('nama_lengkap', 'like', '%' . $query . '%')
                                 ->limit(5)
                                 ->get(['id', 'nik', 'nama_lengkap']);
            foreach ($penduduks as $penduduk) {
                $results[] = [
                    'type' => 'penduduk',
                    'id' => $penduduk->id,
                    'title' => $penduduk->nama_lengkap,
                    'description' => 'NIK: ' . $penduduk->nik,
                    'link' => '/penduduk/' . $penduduk->id, // Example link
                ];
            }

            // Search Kas (by jenis_kas, bulan, tahun, or related penduduk)
            $kas = Kas::where('jenis_kas', 'like', '%' . $query . '%')
                      ->orWhere('bulan', 'like', '%' . $query . '%')
                      ->orWhere('tahun', 'like', '%' . $query . '%')
                      ->orWhereHas('penduduk', function ($q) use ($query) {
                          $q->where('nama_lengkap', 'like', '%' . $query . '%');
                      })
                      ->limit(5)
                      ->get(['id', 'jenis_kas', 'bulan', 'tahun', 'jumlah', 'status']);
            foreach ($kas as $item) {
                $results[] = [
                    'type' => 'kas',
                    'id' => $item->id,
                    'title' => 'Kas ' . $item->jenis_kas . ' ' . $item->bulan . '/' . $item->tahun,
                    'description' => 'Jumlah: ' . $item->jumlah . ', Status: ' . $item->status,
                    'link' => '/kas/' . $item->id, // Example link
                ];
            }

            // Search PaymentInfo (by nama_bank, nomor_rekening)
            $paymentInfos = PaymentInfo::where('bank_transfer->bank_name', 'like', '%' . $query . '%')
                                       ->orWhere('bank_transfer->account_number', 'like', '%' . $query . '%')
                                       ->limit(5)
                                       ->get(['id', 'bank_transfer', 'e_wallet', 'qr_code']);
            foreach ($paymentInfos as $info) {
                $results[] = [
                    'type' => 'payment_info',
                    'id' => $info->id,
                    'title' => 'Info Pembayaran: ' . ($info->bank_transfer['bank_name'] ?? 'N/A'),
                    'description' => 'No. Rek: ' . ($info->bank_transfer['account_number'] ?? 'N/A'),
                    'link' => '/payment-info/' . $info->id, // Example link
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $results
            ]);
        } catch (\Exception $e) {
            Log::error("Error during global search: " . $e->getMessage(), ['query' => $query, 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan pencarian global.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get financial report (e.g., income/expense summary).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getFinancialReport(Request $request)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['rt', 'rw', 'kades', 'admin'])) {
            return response()->json(['error' => 'Akses ditolak'], 403);
        }

        try {
            $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
            $endDate = $request->input('end_date', now()->endOfMonth()->toDateString());

            $query = Kas::whereBetween('tanggal_bayar', [$startDate, $endDate])
                        ->where('status', 'lunas');

            // Apply role-based filtering
            if ($user->role === 'rt' && $user->penduduk && $user->penduduk->rtKetua) {
                $rtId = $user->penduduk->rtKetua->id;
                $query->whereHas('penduduk', function ($q) use ($rtId) {
                    $q->where('rt_id', $rtId);
                });
            } elseif ($user->role === 'rw' && $user->penduduk && $user->penduduk->rwKetua) {
                $rwId = $user->penduduk->rwKetua->id;
                $rtIdsInRw = Rt::where('rw_id', $rwId)->pluck('id');
                $query->whereHas('penduduk', function ($q) use ($rtIdsInRw) {
                    $q->whereIn('rt_id', $rtIdsInRw);
                });
            } elseif ($user->role === 'kades' && $user->penduduk && $user->penduduk->desa_id) {
                $desaId = $user->penduduk->desa_id;
                $rwIdsInDesa = Rw::where('desa_id', $desaId)->pluck('id');
                $rtIdsInDesa = Rt::whereIn('rw_id', $rwIdsInDesa)->pluck('id');
                $query->whereHas('penduduk', function ($q) use ($rtIdsInDesa) {
                    $q->whereIn('rt_id', $rtIdsInDesa);
                });
            }

            $totalIncome = $query->sum('jumlah');
            // Assuming 'pengeluaran' (expenses) would be in a separate table or marked in Kas
            // For now, we'll just show income
            $totalExpenses = 0; // Placeholder

            return response()->json([
                'success' => true,
                'data' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'total_income' => $totalIncome,
                    'total_expenses' => $totalExpenses,
                    'net_balance' => $totalIncome - $totalExpenses,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error("Error getting financial report for user {$user->id}: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat laporan keuangan.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get activity report (e.g., user logins, payment confirmations).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getActivityReport(Request $request)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['rt', 'rw', 'kades', 'admin'])) {
            return response()->json(['error' => 'Akses ditolak'], 403);
        }

        try {
            $startDate = $request->input('start_date', now()->subDays(7)->toDateString());
            $endDate = $request->input('end_date', now()->toDateString());

            // Example: Get recent user logins
            $recentLogins = User::whereBetween('last_activity', [$startDate, $endDate])
                                ->orderBy('last_activity', 'desc')
                                ->limit(10)
                                ->get(['id', 'name', 'email', 'last_activity']);

            $activities = [];
            foreach ($recentLogins as $login) {
                $activities[] = [
                    'type' => 'login',
                    'description' => "Pengguna {$login->name} ({$login->email}) terakhir aktif pada {$login->last_activity->format('d M Y H:i')}.",
                    'timestamp' => $login->last_activity,
                ];
            }

            // Example: Get recent payment confirmations
            $recentConfirmations = Kas::whereBetween('updated_at', [$startDate, $endDate])
                                    ->where('status', 'lunas')
                                    ->orderBy('updated_at', 'desc')
                                    ->limit(10)
                                    ->with('penduduk')
                                    ->get();

            foreach ($recentConfirmations as $kas) {
                $activities[] = [
                    'type' => 'payment_confirmation',
                    'description' => "Pembayaran kas {$kas->jenis_kas} dari {$kas->penduduk->nama_lengkap} dikonfirmasi pada {$kas->updated_at->format('d M Y H:i')}.",
                    'timestamp' => $kas->updated_at,
                ];
            }

            // Sort activities by timestamp
            usort($activities, function($a, $b) {
                return $b['timestamp']->timestamp - $a['timestamp']->timestamp;
            });

            return response()->json([
                'success' => true,
                'data' => $activities
            ]);
        } catch (\Exception $e) {
            Log::error("Error getting activity report for user {$user->id}: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat laporan aktivitas.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export report (placeholder for actual export logic).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function exportReport(Request $request)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['rt', 'rw', 'kades', 'admin'])) {
            return response()->json(['error' => 'Akses ditolak'], 403);
        }

        try {
            $reportType = $request->input('report_type');
            $format = $request->input('format', 'csv'); // csv, pdf, excel

            // In a real application, you would generate the report here
            // and return a downloadable file.
            // Example: using Laravel Excel or Dompdf

            return response()->json([
                'success' => true,
                'message' => "Laporan {$reportType} dalam format {$format} sedang diproses. Anda akan menerima notifikasi setelah siap.",
                'report_type' => $reportType,
                // 'download_url' => '...', // In a real app, provide a temporary download URL
            ]);
        } catch (\Exception $e) {
            Log::error("Error exporting report for user {$user->id}: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengekspor laporan.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
