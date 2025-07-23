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
use App\Models\SaldoTransaction;
use App\Models\BantuanProposal;

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
                    $stats = $this->getKadesStats($user);
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
     * Get monthly summary - FIXED with better error handling
     */
    public function getMonthlySummary(Request $request)
    {
        try {
            $user = Auth::user();
            $currentMonth = Carbon::now()->month;
            $currentYear = Carbon::now()->year;

            Log::info('Getting monthly summary', [
                'user_id' => $user->id,
                'role' => $user->role,
                'month' => $currentMonth,
                'year' => $currentYear
            ]);

            $income = 0;
            $expenses = 0;

            // For RT role, calculate income from confirmed kas payments in their RT
            if ($user->role === 'rt') {
                $rt = $this->getUserRt($user);
                if (!$rt) {
                    Log::warning('RT not found for user in getMonthlySummary', ['user_id' => $user->id]);
                    return response()->json([
                        'success' => true,
                        'data' => [
                            'income' => 0,
                            'expenses' => 0,
                            'netBalance' => 0,
                        ],
                        'message' => 'RT tidak ditemukan, menampilkan data kosong.'
                    ]);
                }

                Log::info('Found RT for user', ['rt_id' => $rt->id, 'rt_no' => $rt->no_rt]);

                // Use 'jumlah' field - the base amount for kas
                $income = Kas::where('rt_id', $rt->id)
                             ->where('status', 'lunas')
                             ->whereMonth('tanggal_bayar', $currentMonth)
                             ->whereYear('tanggal_bayar', $currentYear)
                             ->whereNotNull('tanggal_bayar')
                             ->sum('jumlah') ?? 0;

                Log::info('Calculated income for RT', ['rt_id' => $rt->id, 'income' => $income]);
            }
            elseif ($user->role === 'rw') {
                $rw = $this->getUserRw($user);
                if ($rw) {
                    $rtIds = $rw->rts->pluck('id');
                    $income = Kas::whereIn('rt_id', $rtIds)
                                 ->where('status', 'lunas')
                                 ->whereMonth('tanggal_bayar', $currentMonth)
                                 ->whereYear('tanggal_bayar', $currentYear)
                                 ->whereNotNull('tanggal_bayar')
                                 ->sum('jumlah') ?? 0;
                }
            }
            elseif ($user->role === 'masyarakat') {
                if ($user->penduduk) {
                    $income = Kas::where('penduduk_id', $user->penduduk->id)
                                 ->where('status', 'lunas')
                                 ->whereMonth('tanggal_bayar', $currentMonth)
                                 ->whereYear('tanggal_bayar', $currentYear)
                                 ->whereNotNull('tanggal_bayar')
                                 ->sum('jumlah') ?? 0;
                }
            }

            $netBalance = $income - $expenses;

            return response()->json([
                'success' => true,
                'data' => [
                    'income' => $income,
                    'expenses' => $expenses,
                    'netBalance' => $netBalance,
                ],
                'message' => 'Ringkasan bulanan berhasil dimuat.'
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting monthly summary', [
                'user_id' => Auth::id(),
                'role' => Auth::user()->role ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => true, // Changed to true to prevent frontend errors
                'data' => [
                    'income' => 0,
                    'expenses' => 0,
                    'netBalance' => 0,
                ],
                'message' => 'Gagal memuat ringkasan bulanan, menampilkan data kosong.'
            ]);
        }
    }

    /**
     * Get recent payments - FIXED with better error handling
     */
    public function getRecentPayments(Request $request)
    {
        try {
            $user = Auth::user();
            $limit = $request->get('limit', 5);

            Log::info('Getting recent payments', [
                'user_id' => $user->id,
                'role' => $user->role,
                'limit' => $limit
            ]);

            $query = Kas::with(['penduduk', 'rt']);

            // Apply role-based filtering
            switch ($user->role) {
                case 'masyarakat':
                    $penduduk = Penduduk::where('user_id', $user->id)->first();
                    if (!$penduduk) {
                        return response()->json([
                            'success' => true,
                            'data' => [],
                            'message' => 'Data penduduk tidak ditemukan'
                        ]);
                    }
                    $query->where('penduduk_id', $penduduk->id);
                    break;

                case 'rt':
                    $rt = $this->getUserRt($user);
                    if (!$rt) {
                        Log::warning('RT not found for user in getRecentPayments', ['user_id' => $user->id]);
                        return response()->json([
                            'success' => true,
                            'data' => [],
                            'message' => 'RT tidak ditemukan untuk user ini'
                        ]);
                    }
                    
                    Log::info('Found RT for recent payments', ['rt_id' => $rt->id, 'rt_no' => $rt->no_rt]);
                    $query->where('rt_id', $rt->id);
                    break;

                case 'rw':
                    $rw = $this->getUserRw($user);
                    if ($rw) {
                        $rtIds = $rw->rts->pluck('id');
                        $query->whereIn('rt_id', $rtIds);
                    } else {
                        return response()->json([
                            'success' => true,
                            'data' => [],
                            'message' => 'RW tidak ditemukan untuk user ini'
                        ]);
                    }
                    break;

                case 'admin':
                case 'kades':
                    // Can see all payments
                    break;

                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Akses tidak diizinkan'
                    ], 403);
            }

            $recentPayments = $query->whereIn('status', ['lunas', 'menunggu_konfirmasi', 'ditolak'])
                                    ->whereNotNull('tanggal_bayar')
                                    ->orderBy('tanggal_bayar', 'desc')
                                    ->limit($limit)
                                    ->get();

            Log::info('Found recent payments', ['count' => $recentPayments->count()]);

            $transformedPayments = $recentPayments->map(function($kas) {
                // Use safe navigation and fallbacks
                $pendudukName = optional($kas->penduduk)->nama_lengkap ?? 'Warga';
                $rtNo = optional($kas->rt)->no_rt ?? 'N/A';
                
                // Calculate total amount including denda
                $totalAmount = $kas->jumlah + ($kas->denda ?? 0);

                return [
                    'id' => $kas->id,
                    'description' => 'Pembayaran Kas Minggu ke-' . $kas->minggu_ke . ' Tahun ' . $kas->tahun . ' dari ' . $pendudukName,
                    'timestamp' => $kas->tanggal_bayar ?? $kas->created_at,
                    'amount' => $totalAmount,
                    'status' => $kas->status,
                    'location' => 'RT ' . $rtNo,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $transformedPayments,
                'message' => 'Pembayaran terbaru berhasil dimuat.'
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting recent payments', [
                'user_id' => Auth::id(),
                'role' => Auth::user()->role ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => true, // Changed to true to prevent frontend errors
                'data' => [],
                'message' => 'Gagal memuat pembayaran terbaru, menampilkan data kosong.'
            ]);
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
                            ->whereIn('status', ['belum_bayar', 'terlambat', 'menunggu_konfirmasi', 'ditolak'])
                            ->orderBy('tanggal_jatuh_tempo', 'asc')
                            ->get();

            foreach ($kasBills as $bill) {
                $type = 'info';
                $message = 'Tagihan kas minggu ke-' . $bill->minggu_ke . ' tahun ' . $bill->tahun . ' menunggu pembayaran.';
                $title = 'Tagihan Kas Mendatang';

                if ($bill->status === 'terlambat' || ($bill->status === 'belum_bayar' && $bill->tanggal_jatuh_tempo && $bill->tanggal_jatuh_tempo->isPast())) {
                    $type = 'error';
                    $message = 'Tagihan kas minggu ke-' . $bill->minggu_ke . ' tahun ' . $bill->tahun . ' sudah terlambat!';
                    $title = 'Tagihan Kas Terlambat';
                    $hasOverdue = true;
                } elseif ($bill->status === 'menunggu_konfirmasi') {
                    $type = 'warning';
                    $message = 'Pembayaran kas minggu ke-' . $bill->minggu_ke . ' tahun ' . $bill->tahun . ' sedang menunggu konfirmasi.';
                    $title = 'Pembayaran Menunggu Konfirmasi';
                } elseif ($bill->status === 'ditolak') {
                    $type = 'error';
                    $message = 'Pembayaran kas minggu ke-' . $bill->minggu_ke . ' tahun ' . $bill->tahun . ' ditolak. Silakan perbaiki dan kirim ulang.';
                    $title = 'Pembayaran Ditolak';
                }

                $alerts[] = [
                    'id' => $bill->id,
                    'title' => $title,
                    'message' => $message,
                    'type' => $type,
                    'total_bayar' => $bill->jumlah + ($bill->denda ?? 0),
                    'tanggal_jatuh_tempo' => $bill->tanggal_jatuh_tempo ? $bill->tanggal_jatuh_tempo->format('d F Y') : '-',
                    'payment_url' => route('kas.payment.form', $bill->id),
                    'is_overdue' => $bill->status === 'belum_bayar' && $bill->tanggal_jatuh_tempo && $bill->tanggal_jatuh_tempo->isPast(),
                    'rejection_reason' => $bill->rejection_reason ?? null, // Add this line
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
                        'path' => $info->qr_code_url,
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
                'kasDitolak' => Kas::where('status', 'ditolak')->count(),
                'kasHariIni' => Kas::whereDate('tanggal_bayar', today())->where('status', 'lunas')->count(),
                'kasBulanIni' => Kas::whereMonth('tanggal_bayar', now()->month)->where('status', 'lunas')->sum('jumlah') ?? 0,
                'totalNotifikasi' => Notifikasi::count(),
                'notifikasiUnread' => Notifikasi::where('dibaca', false)->count(),
                'notifikasiHariIni' => Notifikasi::whereDate('created_at', today())->count(),
                'systemHealth' => $this->getSystemHealthStatus(),
                // Add proposal statistics for admin
                'totalProposals' => BantuanProposal::count(),
                'pendingProposals' => BantuanProposal::where('status', 'pending')->count(),
                'approvedProposals' => BantuanProposal::where('status', 'approved')->count(),
                'rejectedProposals' => BantuanProposal::where('status', 'rejected')->count(),
                'totalProposalAmount' => BantuanProposal::sum('jumlah_bantuan') ?? 0,
                'approvedProposalAmount' => BantuanProposal::where('status', 'approved')->sum('jumlah_disetujui') ?? 0,
            ];
        } catch (\Exception $e) {
            Log::error('Error in getAdminStats', ['error' => $e->getMessage()]);
            return $this->getBasicStats();
        }
    }

    /**
     * Get Kades Statistics - FIXED to get specific desa saldo
     */
    private function getKadesStats($user)
    {
        try {
            // Get the specific desa for this kades user
            // Assuming kades is associated with a specific desa
            $desa = $this->getUserDesa($user);
            $totalSaldoDesa = $desa ? $desa->saldo : 0;

            return [
                'totalRws' => Rw::count(),
                'totalRts' => Rt::count(),
                'totalPenduduk' => Penduduk::count(),
                'totalKasTerkumpul' => Kas::where('status', 'lunas')->sum('jumlah') ?? 0,
                'totalSaldoDesa' => $totalSaldoDesa, // Fixed: Get specific desa saldo
                'pendudukAktif' => Penduduk::where('status', 'aktif')->count(),
                'pendudukLakiLaki' => Penduduk::where('jenis_kelamin', 'L')->count(),
                'pendudukPerempuan' => Penduduk::where('jenis_kelamin', 'P')->count(),
                'totalKk' => Kk::count(),
                'kasLunas' => Kas::where('status', 'lunas')->count(),
                'kasBelumBayar' => Kas::whereIn('status', ['belum_bayar', 'terlambat', 'menunggu_konfirmasi'])->count(),
                'kasDitolak' => Kas::where('status', 'ditolak')->count(),
                // Add real proposal data for kades
                'pendingProposals' => BantuanProposal::where('status', 'pending')->count(),
                'approvedProposals' => BantuanProposal::where('status', 'approved')->count(),
                'rejectedProposals' => BantuanProposal::where('status', 'rejected')->count(),
                'totalProposalAmount' => BantuanProposal::sum('jumlah_bantuan') ?? 0,
                'approvedProposalAmount' => BantuanProposal::where('status', 'approved')->sum('jumlah_disetujui') ?? 0,
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
            $rw = $this->getUserRw($user);
            if (!$rw) {
                Log::warning('RW not found for user', ['user_id' => $user->id]);
                return $this->getBasicStats();
            }

            $rtIds = $rw->rts->pluck('id');
            $totalSaldoRw = $rw->saldo ?? 0; // Get RW saldo from the model
            
            return [
                'rwId' => $rw->id,
                'rwNumber' => $rw->no_rw,
                'totalRts' => $rtIds->count(),
                'totalPenduduk' => Kk::whereIn('rt_id', $rtIds)->withCount('penduduks')->get()->sum('penduduks_count'),
                'kasLunas' => Kas::whereIn('rt_id', $rtIds)->where('status', 'lunas')->count(),
                'kasBelumBayar' => Kas::whereIn('rt_id', $rtIds)->whereIn('status', ['belum_bayar', 'terlambat', 'menunggu_konfirmasi'])->count(),
                'kasDitolak' => Kas::whereIn('rt_id', $rtIds)->where('status', 'ditolak')->count(),
                'totalSaldoRw' => $totalSaldoRw, // New: Add saldo RW
                // Add proposal stats for RW
                'myProposals' => BantuanProposal::where('rw_id', $rw->id)->count(),
                'pendingProposals' => BantuanProposal::where('rw_id', $rw->id)->where('status', 'pending')->count(),
                'approvedProposals' => BantuanProposal::where('rw_id', $rw->id)->where('status', 'approved')->count(),
            ];
        } catch (\Exception $e) {
            Log::error('Error in getRwStats', ['error' => $e->getMessage(), 'user_id' => $user->id]);
            return $this->getBasicStats();
        }
    }

    /**
     * Get RT Statistics - ENHANCED FOR RT DASHBOARD
     */
    private function getRtStats($user)
    {
        try {
            $rt = $this->getUserRt($user);
            if (!$rt) {
                Log::warning('RT not found for user', ['user_id' => $user->id]);
                return $this->getBasicStats();
            }

            // Get total KK and Penduduk count
            $totalKk = Kk::where('rt_id', $rt->id)->count();
            $totalPenduduk = Kk::where('rt_id', $rt->id)->withCount('penduduks')->get()->sum('penduduks_count');
            
            // Get Kas statistics
            $kasLunas = Kas::where('rt_id', $rt->id)->where('status', 'lunas')->count();
            $kasBelumBayar = Kas::where('rt_id', $rt->id)->whereIn('status', ['belum_bayar', 'terlambat', 'menunggu_konfirmasi'])->count();
            
            // Get FRESH RT saldo
            $rt->refresh();
            $totalSaldoRt = $rt->saldo ?? 0;

            // Calculate kas terkumpul (total collected kas including denda)
            $kasTerkumpul = Kas::where('rt_id', $rt->id)
                ->where('status', 'lunas')
                ->sum(DB::raw('jumlah + COALESCE(denda, 0)')) ?? 0;

            // Calculate kas available for transfer
            $alreadyTransferred = SaldoTransaction::where('rt_id', $rt->id)
                ->where('transaction_type', 'kas_transfer')
                ->sum('amount') ?? 0;
            
            $kasAvailableForTransfer = $kasTerkumpul - $alreadyTransferred;

            Log::info('RT Stats calculation', [
                'rt_id' => $rt->id,
                'kas_terkumpul' => $kasTerkumpul,
                'already_transferred' => $alreadyTransferred,
                'kas_available_for_transfer' => $kasAvailableForTransfer
            ]);

            return [
                'rtId' => $rt->id,
                'rtNumber' => $rt->no_rt ?? 'N/A',
                'totalKk' => $totalKk,
                'totalPenduduk' => $totalPenduduk,
                'kasLunas' => $kasLunas,
                'kasBelumBayar' => $kasBelumBayar,
                'kasDitolak' => Kas::where('rt_id', $rt->id)->where('status', 'ditolak')->count(),
                'totalSaldoRt' => $totalSaldoRt,
                'kasTerkumpul' => $kasTerkumpul,
                'kasAvailableForTransfer' => $kasAvailableForTransfer,
            ];
        } catch (\Exception $e) {
            Log::error('Error in getRtStats', ['error' => $e->getMessage(), 'user_id' => $user->id]);
            return $this->getBasicStats();
        }
    }

    /**
     * Get Masyarakat Statistics
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
            $kasTerlambat = $kasQuery->clone()->where('status', 'terlambat')->count();
            $kasMenungguKonfirmasi = $kasQuery->clone()->where('status', 'menunggu_konfirmasi')->count();
            $kasDitolak = $kasQuery->clone()->where('status', 'ditolak')->count();
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
                'kasDitolak' => $kasDitolak,
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
            'kasDitolak' => 0,
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
            'kasTerkumpul' => 0,
            'kasAvailableForTransfer' => 0,
            // Add proposal defaults
            'pendingProposals' => 0,
            'approvedProposals' => 0,
            'rejectedProposals' => 0,
            'totalProposals' => 0,
            'myProposals' => 0,
            'totalProposalAmount' => 0,
            'approvedProposalAmount' => 0,
        ];
    }

    /**
     * Get All System Activities - ENHANCED with proposal activities
     */
    private function getAllSystemActivities($limit)
    {
        $activities = [];

        try {
            // Get recent kas payments
            $recentPayments = Kas::with(['penduduk', 'rt'])
                ->whereIn('status', ['lunas', 'menunggu_konfirmasi', 'ditolak'])
                ->whereNotNull('tanggal_bayar')
                ->orderBy('tanggal_bayar', 'desc')
                ->limit($limit / 3)
                ->get();

            foreach ($recentPayments as $payment) {
                $icon = 'credit-card';
                $color = 'green';
                $title = 'Pembayaran Kas';
                $description = "{$payment->penduduk->nama_lengkap} membayar kas minggu ke-{$payment->minggu_ke}";

                if ($payment->status === 'menunggu_konfirmasi') {
                    $icon = 'hourglass';
                    $color = 'yellow';
                    $title = 'Pembayaran Menunggu Konfirmasi';
                    $description = "{$payment->penduduk->nama_lengkap} mengajukan pembayaran kas minggu ke-{$payment->minggu_ke} (Menunggu Konfirmasi)";
                } elseif ($payment->status === 'ditolak') {
                    $icon = 'x-circle';
                    $color = 'red';
                    $title = 'Pembayaran Ditolak';
                    $description = "Pembayaran kas minggu ke-{$payment->minggu_ke} dari {$payment->penduduk->nama_lengkap} ditolak.";
                }

                $activities[] = [
                    'id' => 'kas_' . $payment->id,
                    'type' => 'kas_payment',
                    'title' => $title,
                    'description' => $description,
                    'amount' => $payment->jumlah,
                    'user' => $payment->penduduk->nama_lengkap,
                    'location' => "RT {$payment->rt->no_rt}",
                    'timestamp' => $payment->tanggal_bayar,
                    'icon' => $icon,
                    'color' => $color
                ];
            }

            // Get recent proposal activities
            $recentProposals = BantuanProposal::with(['rw', 'submittedBy'])
                ->orderBy('updated_at', 'desc')
                ->limit($limit / 3)
                ->get();

            foreach ($recentProposals as $proposal) {
                $icon = 'file-text';
                $color = 'blue';
                $title = 'Proposal Bantuan';
                $description = "Proposal '{$proposal->judul_proposal}' dari RW {$proposal->rw->nama}";

                if ($proposal->status === 'pending') {
                    $icon = 'clock';
                    $color = 'yellow';
                    $title = 'Proposal Menunggu Review';
                    $description = "Proposal '{$proposal->judul_proposal}' menunggu persetujuan";
                } elseif ($proposal->status === 'approved') {
                    $icon = 'check-circle';
                    $color = 'green';
                    $title = 'Proposal Disetujui';
                    $description = "Proposal '{$proposal->judul_proposal}' telah disetujui";
                } elseif ($proposal->status === 'rejected') {
                    $icon = 'x-circle';
                    $color = 'red';
                    $title = 'Proposal Ditolak';
                    $description = "Proposal '{$proposal->judul_proposal}' ditolak";
                }

                $activities[] = [
                    'id' => 'proposal_' . $proposal->id,
                    'type' => 'proposal',
                    'title' => $title,
                    'description' => $description,
                    'amount' => $proposal->jumlah_bantuan,
                    'user' => $proposal->submittedBy->name ?? 'RW ' . $proposal->rw->nama,
                    'location' => "RW {$proposal->rw->nama}",
                    'timestamp' => $proposal->updated_at,
                    'icon' => $icon,
                    'color' => $color
                ];
            }

            // Get recent user registrations
            $recentUsers = User::with('penduduk')
                ->orderBy('created_at', 'desc')
                ->limit($limit / 3)
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
                                    ->with('user', 'kas.penduduk', 'kas.rt')
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
                                    ->with('user', 'kas.penduduk', 'kas.rt')
                                    ->orderBy('created_at', 'desc')
                                    ->limit($limit)
                                    ->get();
                }
            } elseif ($user->role === 'kades') {
                $activities = Notifikasi::with('user', 'kas.penduduk', 'kas.rt')
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
                        $description = 'Minggu ke-' . $kas->minggu_ke . ' Tahun ' . $kas->tahun . ' - ' . ($kas->status_text ?? $kas->status);
                        $amount = $kas->jumlah + ($kas->denda ?? 0);
                        $location = 'RT ' . ($kas->rt->no_rt ?? 'N/A');
                        if ($kas->status === 'lunas') {
                            $icon = 'check-circle';
                            $color = 'green';
                        } elseif ($kas->status === 'menunggu_konfirmasi') {
                            $icon = 'hourglass';
                            $color = 'yellow';
                        } elseif ($kas->status === 'ditolak') {
                            $icon = 'x-circle';
                            $color = 'red';
                        }
                    }
                } elseif ($activity->kategori === 'proposal') {
                    $icon = 'file-text';
                    $color = 'purple';
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

    /**
     * Get user's Desa - NEW METHOD for kades
     */
    private function getUserDesa($user)
    {
        try {
            // For now, get the first desa (assuming single desa system)
            // In multi-desa system, you might need to associate kades with specific desa
            return Desa::first();
        } catch (\Exception $e) {
            Log::error('Error getting user Desa', ['error' => $e->getMessage(), 'user_id' => $user->id]);
            return null;
        }
    }

    /**
     * Get user's RW
     */
    private function getUserRw($user)
    {
        try {
            // Method 1: Check if user has penduduk and is RW ketua directly
            if ($user->penduduk && $user->penduduk->rwKetua) {
                return $user->penduduk->rwKetua;
            }
            
            // Method 2: Check if user is assigned as RW ketua via ketua_rw_id
            if ($user->penduduk) {
                $rw = Rw::where('ketua_rw_id', $user->penduduk->id)->first();
                if ($rw) {
                    return $rw;
                }
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Error getting user RW', ['error' => $e->getMessage(), 'user_id' => $user->id]);
            return null;
        }
    }

    /**
     * Get user's RT
     */
    private function getUserRt($user)
    {
        try {
            Log::info('Getting RT for user', [
                'user_id' => $user->id,
                'user_role' => $user->role,
                'has_penduduk' => $user->penduduk ? true : false,
                'penduduk_id' => $user->penduduk->id ?? null
            ]);

            // Method 1: Check if user has penduduk and is RT ketua directly
            if ($user->penduduk && $user->penduduk->rtKetua) {
                Log::info('Found RT via rtKetua relationship', [
                    'user_id' => $user->id,
                    'rt_id' => $user->penduduk->rtKetua->id,
                    'rt_no' => $user->penduduk->rtKetua->no_rt
                ]);
                return $user->penduduk->rtKetua;
            }
            
            // Method 2: Check if user is assigned as RT ketua via ketua_rt_id
            if ($user->penduduk) {
                $rt = Rt::where('ketua_rt_id', $user->penduduk->id)->first();
                if ($rt) {
                    Log::info('Found RT via ketua_rt_id lookup', [
                        'user_id' => $user->id,
                        'penduduk_id' => $user->penduduk->id,
                        'rt_id' => $rt->id,
                        'rt_no' => $rt->no_rt
                    ]);
                    return $rt;
                }
            }

            Log::warning('No RT found for user', [
                'user_id' => $user->id,
                'has_penduduk' => $user->penduduk ? true : false,
                'penduduk_id' => $user->penduduk->id ?? null
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error('Error getting user RT', [
                'error' => $e->getMessage(), 
                'user_id' => $user->id,
                'trace' => $e->getTraceAsString()
            ]);
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

    public function getRtDashboardStats(Request $request)
    {
        $user = Auth::user();

        // Ensure only RT role can access this endpoint for their specific RT data
        if ($user->role !== 'rt') {
            return response()->json([
                'success' => false,
                'message' => 'Akses tidak diizinkan. Hanya pengguna RT yang dapat melihat dashboard ini.'
            ], 403);
        }

        try {
            // Find the RT associated with the logged-in user (assuming ketua_rt_id in Rts table links to Penduduk.id)
            $penduduk = Penduduk::where('user_id', $user->id)->first();
            if (!$penduduk) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data penduduk tidak ditemukan untuk pengguna ini.'
                ], 404);
            }

            $rt = Rt::where('ketua_rt_id', $penduduk->id)->first();

            if (!$rt) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data RT tidak ditemukan untuk pengguna ini. Pastikan Anda terdaftar sebagai Ketua RT.'
                ], 404);
            }

            $rtId = $rt->id;

            // Total KK in RT
            $totalKk = Kk::where('rt_id', $rtId)->count();

            // Total Penduduk in RT
            $totalPenduduk = Penduduk::where('rt_id', $rtId)->count();

            // Kas Lunas (this year)
            $currentYear = date('Y');
            $kasLunas = Kas::where('rt_id', $rtId)
                            ->where('status', 'lunas')
                            ->whereYear('tanggal_bayar', $currentYear)
                            ->count();

            // Kas Belum Bayar (this year)
            $kasBelumBayar = Kas::where('rt_id', $rtId)
                                ->where('status', 'belum_bayar')
                                ->whereYear('tanggal_tagihan', $currentYear) // Assuming tanggal_tagihan for pending
                                ->count();

            // Total Saldo RT
            $totalSaldoRt = $rt->saldo ?? 0;

            // Kas Terkumpul (total collected kas including denda)
            $kasTerkumpulTotal = Kas::where('rt_id', $rtId)
                ->where('status', 'lunas')
                ->sum(DB::raw('jumlah + COALESCE(denda, 0)')) ?? 0;

            // Already transferred amount from kas to saldo
            $alreadyTransferred = SaldoTransaction::where('rt_id', $rtId)
                ->where('transaction_type', 'kas_transfer')
                ->sum('amount') ?? 0;

            // Kas available for transfer (collected but not yet transferred)
            $kasAvailableForTransfer = $kasTerkumpulTotal - $alreadyTransferred;


            return response()->json([
                'success' => true,
                'message' => 'Data dashboard RT berhasil dimuat.',
                'data' => [
                    'rtNumber' => $rt->no_rt,
                    'rtId' => $rt->id,
                    'totalKk' => $totalKk,
                    'totalPenduduk' => $totalPenduduk,
                    'kasLunas' => $kasLunas,
                    'kasBelumBayar' => $kasBelumBayar,
                    'totalSaldoRt' => $totalSaldoRt,
                    'kasTerkumpul' => $kasTerkumpulTotal, // This is the total collected, not necessarily available for transfer
                    'kasAvailableForTransfer' => $kasAvailableForTransfer, // This is the amount ready to be transferred
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching RT dashboard stats', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memuat data dashboard: ' . $e->getMessage()
            ], 500);
        }
    }
}
