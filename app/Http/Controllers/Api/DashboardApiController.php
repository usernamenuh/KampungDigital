<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Desa;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Exception;

class DashboardApiController extends Controller
{
    /**
     * Get dashboard statistics
     */
    public function getStats(): JsonResponse
    {
        try {
            $stats = Cache::remember('dashboard_stats', 300, function () {
                $totalDesa = Desa::count();
                $desaAktif = Desa::where('status', 'aktif')->count();
                $desaTidakAktif = $totalDesa - $desaAktif;
                
                // Mock data for other statistics
                $totalPenduduk = 2437;
                $lakiLaki = 1256;
                $perempuan = 1181;
                $aktivitasHariIni = rand(8, 15);
                
                return [
                    'totalDesa' => $totalDesa,
                    'desaAktif' => $desaAktif,
                    'desaTidakAktif' => $desaTidakAktif,
                    'totalPenduduk' => $totalPenduduk,
                    'lakiLaki' => $lakiLaki,
                    'perempuan' => $perempuan,
                    'aktivitasHariIni' => $aktivitasHariIni
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $stats,
                'timestamp' => now()->toISOString()
            ]);

        } catch (Exception $e) {
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
                            'label' => 'Pendapatan',
                            'data' => [15, 18, 22, 25, 28, 32],
                            'backgroundColor' => '#8B5CF6'
                        ]
                    ]
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $monthlyData
            ]);

        } catch (Exception $e) {
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
                return [
                    'male' => 1256,
                    'female' => 1181,
                    'total' => 2437
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $genderData
            ]);

        } catch (Exception $e) {
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
                            'label' => 'Revenue Trend',
                            'data' => [50, 45, 60, 55, 70, 65],
                            'borderColor' => '#8B5CF6',
                            'backgroundColor' => 'rgba(139, 92, 246, 0.1)'
                        ]
                    ]
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $revenueData
            ]);

        } catch (Exception $e) {
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
                    'data' => [35, 25, 20, 15, 5],
                    'backgroundColor' => ['#8B5CF6', '#06B6D4', '#10B981', '#F59E0B', '#EF4444']
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $categoryData
            ]);

        } catch (Exception $e) {
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
                        'timestamp' => now()->subMinutes(2)->toISOString(),
                        'user' => 'Admin'
                    ],
                    [
                        'id' => 2,
                        'title' => 'Data Penduduk Diperbarui',
                        'description' => 'Update data penduduk Desa Makmur',
                        'type' => 'info',
                        'icon' => 'users',
                        'timestamp' => now()->subMinutes(5)->toISOString(),
                        'user' => 'Admin'
                    ],
                    [
                        'id' => 3,
                        'title' => 'Backup Data Berhasil',
                        'description' => 'Backup otomatis database telah selesai',
                        'type' => 'success',
                        'icon' => 'database',
                        'timestamp' => now()->subMinutes(10)->toISOString(),
                        'user' => 'System'
                    ],
                    [
                        'id' => 4,
                        'title' => 'Peringatan Sistem',
                        'description' => 'Penggunaan storage mencapai 80%',
                        'type' => 'warning',
                        'icon' => 'alert-triangle',
                        'timestamp' => now()->subMinutes(15)->toISOString(),
                        'user' => 'System'
                    ]
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $activities
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch activities',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test connection endpoint
     */
    public function testConnection(): JsonResponse
    {
        try {
            // Test database connection
            DB::connection()->getPdo();
            
            return response()->json([
                'success' => true,
                'message' => 'Connection successful',
                'timestamp' => now()->toISOString(),
                'server_time' => now()->format('H:i:s'),
                'status' => 'online'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Connection failed',
                'error' => $e->getMessage(),
                'status' => 'offline'
            ], 500);
        }
    }

    /**
     * Clear dashboard cache
     */
    public function clearCache(): JsonResponse
    {
        try {
            Cache::forget('dashboard_stats');
            Cache::forget('monthly_data');
            Cache::forget('gender_data');
            Cache::forget('revenue_data');
            Cache::forget('category_data');
            Cache::forget('recent_activities');

            return response()->json([
                'success' => true,
                'message' => 'Cache cleared successfully'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cache',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get system status
     */
    public function getSystemStatus(): JsonResponse
    {
        try {
            $status = [
                'database' => $this->checkDatabaseConnection(),
                'cache' => $this->checkCacheConnection(),
                'storage' => $this->checkStorageSpace(),
                'memory' => $this->getMemoryUsage(),
                'uptime' => $this->getSystemUptime()
            ];

            return response()->json([
                'success' => true,
                'data' => $status,
                'overall_status' => $this->getOverallStatus($status)
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get system status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check database connection
     */
    private function checkDatabaseConnection(): array
    {
        try {
            $start = microtime(true);
            DB::connection()->getPdo();
            $responseTime = round((microtime(true) - $start) * 1000, 2);

            return [
                'status' => 'online',
                'response_time' => $responseTime . 'ms',
                'message' => 'Database connection successful'
            ];
        } catch (Exception $e) {
            return [
                'status' => 'offline',
                'response_time' => null,
                'message' => 'Database connection failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Check cache connection
     */
    private function checkCacheConnection(): array
    {
        try {
            $testKey = 'cache_test_' . time();
            Cache::put($testKey, 'test', 10);
            $value = Cache::get($testKey);
            Cache::forget($testKey);

            return [
                'status' => $value === 'test' ? 'online' : 'offline',
                'message' => $value === 'test' ? 'Cache working properly' : 'Cache not working'
            ];
        } catch (Exception $e) {
            return [
                'status' => 'offline',
                'message' => 'Cache connection failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Check storage space
     */
    private function checkStorageSpace(): array
    {
        try {
            $bytes = disk_free_space(storage_path());
            $totalBytes = disk_total_space(storage_path());
            $usedBytes = $totalBytes - $bytes;
            $usedPercentage = round(($usedBytes / $totalBytes) * 100, 2);

            return [
                'free_space' => $this->formatBytes($bytes),
                'total_space' => $this->formatBytes($totalBytes),
                'used_space' => $this->formatBytes($usedBytes),
                'used_percentage' => $usedPercentage,
                'status' => $usedPercentage > 90 ? 'critical' : ($usedPercentage > 80 ? 'warning' : 'good')
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Unable to check storage space: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get memory usage
     */
    private function getMemoryUsage(): array
    {
        $memoryUsage = memory_get_usage(true);
        $memoryPeak = memory_get_peak_usage(true);
        $memoryLimit = ini_get('memory_limit');

        return [
            'current_usage' => $this->formatBytes($memoryUsage),
            'peak_usage' => $this->formatBytes($memoryPeak),
            'memory_limit' => $memoryLimit,
            'usage_percentage' => round(($memoryUsage / $this->parseBytes($memoryLimit)) * 100, 2)
        ];
    }

    /**
     * Get system uptime (mock)
     */
    private function getSystemUptime(): array
    {
        return [
            'uptime' => '2 days, 14 hours, 32 minutes',
            'started_at' => now()->subDays(2)->subHours(14)->subMinutes(32)->toISOString()
        ];
    }

    /**
     * Get overall system status
     */
    private function getOverallStatus(array $status): string
    {
        if ($status['database']['status'] === 'offline') {
            return 'critical';
        }

        if (isset($status['storage']['status']) && $status['storage']['status'] === 'critical') {
            return 'critical';
        }

        if (isset($status['storage']['status']) && $status['storage']['status'] === 'warning') {
            return 'warning';
        }

        return 'good';
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }

    /**
     * Parse bytes from string (like "128M")
     */
    private function parseBytes(string $value): int
    {
        $value = trim($value);
        $last = strtolower($value[strlen($value) - 1]);
        $value = (int) $value;

        switch ($last) {
            case 'g':
                $value *= 1024;
            case 'm':
                $value *= 1024;
            case 'k':
                $value *= 1024;
        }

        return $value;
    }
}
