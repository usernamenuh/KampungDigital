<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Desa;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class DashboardApiController extends Controller
{
    /**
     * Test API connection
     */
    public function test(): JsonResponse
    {
        try {
            // Test database connection
            DB::connection()->getPdo();
            
            return response()->json([
                'success' => true,
                'message' => 'API connection successful',
                'timestamp' => now()->toISOString(),
                'server_time' => now()->format('H:i:s'),
                'status' => 'online',
                'database' => 'connected'
            ]);

        } catch (Exception $e) {
            Log::error('API test failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'API connection failed',
                'error' => $e->getMessage(),
                'status' => 'offline'
            ], 500);
        }
    }

    /**
     * Get dashboard statistics
     */
    public function getStats(): JsonResponse
    {
        try {
            $stats = Cache::remember('dashboard_stats', 300, function () {
                $totalDesa = Desa::count();
                $desaAktif = Desa::where('status', 'aktif')->count();
                $desaTidakAktif = Desa::where('status', 'tidak_aktif')->count();
                $totalPenduduk = Desa::sum('jumlah_penduduk') ?: 2437;
                
                // Calculate gender distribution (mock data based on total population)
                $lakiLaki = (int) ($totalPenduduk * 0.515); // 51.5% male
                $perempuan = $totalPenduduk - $lakiLaki;
                
                return [
                    'totalDesa' => max($totalDesa, 13),
                    'desaAktif' => max($desaAktif, 13),
                    'desaTidakAktif' => $desaTidakAktif,
                    'totalPenduduk' => $totalPenduduk,
                    'lakiLaki' => $lakiLaki,
                    'perempuan' => $perempuan,
                    'aktivitasHariIni' => rand(8, 15),
                    'totalUmkm' => 44,
                    'umkmAktif' => 41,
                    'saldoKas' => 125750000,
                    'programAktif' => 13,
                    'beritaTerbaru' => 28,
                    'wisataDestinasi' => 8,
                    'dokumenTersimpan' => 156,
                    'pesanMasuk' => 24
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $stats,
                'timestamp' => now()->toISOString()
            ]);

        } catch (Exception $e) {
            Log::error('Error fetching dashboard stats: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch dashboard stats',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get monthly data for charts
     */
    public function getMonthlyData(): JsonResponse
    {
        try {
            $monthlyData = Cache::remember('monthly_data', 300, function () {
                return [
                    'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
                    'datasets' => [
                        [
                            'label' => 'Pendapatan (Juta)',
                            'data' => [15, 18, 22, 25, 28, 32],
                            'backgroundColor' => '#8B5CF6',
                            'borderColor' => '#7C3AED',
                            'borderWidth' => 1,
                            'borderRadius' => 8
                        ]
                    ]
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $monthlyData
            ]);

        } catch (Exception $e) {
            Log::error('Error fetching monthly data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch monthly data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get gender distribution data
     */
    public function getGenderData(): JsonResponse
    {
        try {
            $genderData = Cache::remember('gender_data', 300, function () {
                $totalPenduduk = Desa::sum('jumlah_penduduk') ?: 2437;
                $lakiLaki = (int) ($totalPenduduk * 0.515);
                $perempuan = $totalPenduduk - $lakiLaki;
                
                return [
                    'male' => $lakiLaki,
                    'female' => $perempuan,
                    'total' => $totalPenduduk
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $genderData
            ]);

        } catch (Exception $e) {
            Log::error('Error fetching gender data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch gender data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get revenue data for charts
     */
    public function getRevenueData(): JsonResponse
    {
        try {
            $revenueData = Cache::remember('revenue_data', 300, function () {
                return [
                    'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
                    'datasets' => [
                        [
                            'label' => 'Tren Pendapatan',
                            'data' => [50, 45, 60, 55, 70, 65],
                            'borderColor' => '#8B5CF6',
                            'backgroundColor' => 'rgba(139, 92, 246, 0.1)',
                            'borderWidth' => 3,
                            'fill' => true,
                            'tension' => 0.4
                        ]
                    ]
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $revenueData
            ]);

        } catch (Exception $e) {
            Log::error('Error fetching revenue data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch revenue data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get category distribution data
     */
    public function getCategoryData(): JsonResponse
    {
        try {
            $categoryData = Cache::remember('category_data', 300, function () {
                return [
                    'labels' => ['Kuliner', 'Kerajinan', 'Pertanian', 'Jasa', 'Lainnya'],
                    'datasets' => [
                        [
                            'data' => [35, 25, 20, 15, 5],
                            'backgroundColor' => [
                                '#8B5CF6', '#06B6D4', '#10B981', '#F59E0B', '#EF4444'
                            ],
                            'borderWidth' => 0
                        ]
                    ]
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $categoryData
            ]);

        } catch (Exception $e) {
            Log::error('Error fetching category data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch category data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get recent activities
     */
    public function getActivities(): JsonResponse
    {
        try {
            $activities = Cache::remember('recent_activities', 60, function () {
                return [
                    [
                        'id' => 1,
                        'title' => 'Desa Baru Ditambahkan',
                        'description' => 'Desa Sukamaju berhasil didaftarkan ke sistem',
                        'type' => 'success',
                        'icon' => 'plus-circle',
                        'time' => '2 menit yang lalu',
                        'timestamp' => now()->subMinutes(2)->toISOString()
                    ],
                    [
                        'id' => 2,
                        'title' => 'Data Penduduk Diperbarui',
                        'description' => 'Update data penduduk Desa Makmur',
                        'type' => 'info',
                        'icon' => 'users',
                        'time' => '5 menit yang lalu',
                        'timestamp' => now()->subMinutes(5)->toISOString()
                    ],
                    [
                        'id' => 3,
                        'title' => 'Backup Data Berhasil',
                        'description' => 'Backup otomatis database telah selesai',
                        'type' => 'success',
                        'icon' => 'database',
                        'time' => '10 menit yang lalu',
                        'timestamp' => now()->subMinutes(10)->toISOString()
                    ],
                    [
                        'id' => 4,
                        'title' => 'Peringatan Sistem',
                        'description' => 'Penggunaan storage mencapai 80%',
                        'type' => 'warning',
                        'icon' => 'alert-triangle',
                        'time' => '15 menit yang lalu',
                        'timestamp' => now()->subMinutes(15)->toISOString()
                    ]
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $activities
            ]);

        } catch (Exception $e) {
            Log::error('Error fetching activities: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch activities',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear dashboard cache
     */
    public function clearCache(): JsonResponse
    {
        try {
            $cacheKeys = [
                'dashboard_stats',
                'monthly_data',
                'gender_data',
                'revenue_data',
                'category_data',
                'recent_activities'
            ];

            foreach ($cacheKeys as $key) {
                Cache::forget($key);
            }

            return response()->json([
                'success' => true,
                'message' => 'Cache cleared successfully'
            ]);

        } catch (Exception $e) {
            Log::error('Error clearing cache: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cache',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
