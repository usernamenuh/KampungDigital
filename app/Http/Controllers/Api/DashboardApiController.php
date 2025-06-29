<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Desa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardApiController extends Controller
{
    public function test()
    {
        return response()->json([
            'success' => true,
            'message' => 'API is working perfectly!',
            'timestamp' => now(),
            'server_time' => date('Y-m-d H:i:s'),
            'environment' => app()->environment()
        ]);
    }

    public function stats()
    {
        try {
            Log::info('Dashboard stats requested');
        
            $totalDesa = Desa::count();
            Log::info('Total desa: ' . $totalDesa);
    
            $stats = [
                [
                    'title' => 'Total Desa',
                    'value' => (string)$totalDesa,
                    'change' => '+2.5%',
                    'changeType' => 'positive',
                    'icon' => 'building-2',
                    'iconColor' => 'text-blue-600',
                    'description' => 'Desa terdaftar',
                    'subCards' => [
                        ['label' => 'Aktif', 'value' => (string)$totalDesa],
                        ['label' => 'Tidak Aktif', 'value' => '0']
                    ]
                ],
                [
                    'title' => 'Total Penduduk',
                    'value' => '2,437',
                    'change' => '+1.2%',
                    'changeType' => 'positive',
                    'icon' => 'users',
                    'iconColor' => 'text-green-600',
                    'description' => 'Jiwa terdaftar',
                    'subCards' => [
                        ['label' => 'Laki-laki', 'value' => '1,256'],
                        ['label' => 'Perempuan', 'value' => '1,181']
                    ]
                ],
                [
                    'title' => 'UMKM Aktif',
                    'value' => '44',
                    'change' => '+5.1%',
                    'changeType' => 'positive',
                    'icon' => 'store',
                    'iconColor' => 'text-purple-600',
                    'description' => 'Usaha terdaftar'
                ],
                [
                    'title' => 'Saldo Kas',
                    'value' => 'Rp 125.750.000',
                    'change' => '+8.3%',
                    'changeType' => 'positive',
                    'icon' => 'banknote',
                    'iconColor' => 'text-emerald-600',
                    'description' => 'Dana tersedia',
                    'subCards' => [
                        ['label' => 'Pemasukan', 'value' => 'Rp 45.2M'],
                        ['label' => 'Pengeluaran', 'value' => 'Rp 32.8M']
                    ]
                ],
                [
                    'title' => 'Program Aktif',
                    'value' => '13',
                    'change' => '0%',
                    'changeType' => 'stable',
                    'icon' => 'calendar',
                    'iconColor' => 'text-orange-600',
                    'description' => 'Program berjalan'
                ],
                [
                    'title' => 'Berita',
                    'value' => '28',
                    'change' => '+8.1%',
                    'changeType' => 'positive',
                    'icon' => 'newspaper',
                    'iconColor' => 'text-indigo-600',
                    'description' => 'Berita terpublikasi'
                ]
            ];

            Log::info('Stats prepared: ' . count($stats) . ' items');

            return response()->json([
                'success' => true,
                'data' => $stats,
                'timestamp' => now()->toISOString(),
                'total_items' => count($stats)
            ], 200, [
                'Content-Type' => 'application/json',
                'Access-Control-Allow-Origin' => '*'
            ]);
        } catch (\Exception $e) {
            Log::error('Error in stats API: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading stats: ' . $e->getMessage()
            ], 500);
        }
    }

    public function balance()
    {
        try {
            // Simulate balance calculation
            $saldo = 125750000; // Rp 125.750.000
            
            return response()->json([
                'success' => true,
                'data' => [
                    'saldo' => $saldo,
                    'formatted' => 'Rp ' . number_format($saldo, 0, ',', '.'),
                    'pemasukan' => 45200000,
                    'pengeluaran' => 32800000,
                    'pemasukan_formatted' => 'Rp 45.200.000',
                    'pengeluaran_formatted' => 'Rp 32.800.000'
                ],
                'timestamp' => now()->toISOString()
            ]);
        } catch (\Exception $e) {
            Log::error('Error in balance API: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading balance: ' . $e->getMessage()
            ], 500);
        }
    }

    public function genderData()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'male' => 1256,
                'female' => 1181,
                'total' => 2437
            ],
            'timestamp' => now()->toISOString()
        ]);
    }

    public function activities()
    {
        $activities = [
            ['action' => 'Data desa baru ditambahkan', 'time' => '2 menit yang lalu'],
            ['action' => 'Saldo kas diperbarui', 'time' => '10 menit yang lalu'],
            ['action' => 'Laporan bulanan diperbarui', 'time' => '15 menit yang lalu'],
            ['action' => 'UMKM baru terdaftar', 'time' => '1 jam yang lalu'],
            ['action' => 'Program pembangunan dimulai', 'time' => '2 jam yang lalu'],
            ['action' => 'Berita baru dipublikasi', 'time' => '3 jam yang lalu']
        ];

        return response()->json([
            'success' => true,
            'data' => $activities
        ]);
    }

    public function monthlyData()
    {
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'];
        $data = [
            'labels' => $months,
            'datasets' => [
                [
                    'label' => 'Penduduk',
                    'data' => [400, 420, 435, 450, 465, 480],
                    'borderColor' => '#3B82F6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true
                ],
                [
                    'label' => 'UMKM',
                    'data' => [35, 38, 40, 42, 43, 44],
                    'borderColor' => '#10B981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true
                ]
            ]
        ];

        return response()->json(['success' => true, 'data' => $data]);
    }

    // Other methods remain the same...
    public function revenueData()
    {
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'];
        $data = [
            'labels' => $months,
            'datasets' => [
                [
                    'label' => 'Pendapatan',
                    'data' => [15000000, 18000000, 22000000, 25000000, 28000000, 32000000],
                    'backgroundColor' => '#8B5CF6'
                ]
            ]
        ];

        return response()->json(['success' => true, 'data' => $data]);
    }

    public function categoryData()
    {
        $data = [
            'labels' => ['Kuliner', 'Kerajinan', 'Pertanian', 'Jasa', 'Lainnya'],
            'datasets' => [
                [
                    'data' => [30, 25, 20, 15, 10],
                    'backgroundColor' => ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6']
                ]
            ]
        ];

        return response()->json(['success' => true, 'data' => $data]);
    }

    public function populationTrend()
    {
        $years = ['2019', '2020', '2021', '2022', '2023', '2024'];
        $data = [
            'labels' => $years,
            'datasets' => [
                [
                    'label' => 'Populasi',
                    'data' => [2100, 2250, 2400, 2600, 2800, 3000],
                    'borderColor' => '#10B981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.2)',
                    'fill' => true
                ]
            ]
        ];

        return response()->json(['success' => true, 'data' => $data]);
    }

    public function ageDistribution()
    {
        $data = [
            'labels' => ['0-17', '18-30', '31-45', '46-60', '60+'],
            'datasets' => [
                [
                    'label' => 'Laki-laki',
                    'data' => [300, 450, 400, 250, 150],
                    'backgroundColor' => '#3B82F6'
                ],
                [
                    'label' => 'Perempuan',
                    'data' => [280, 420, 380, 230, 140],
                    'backgroundColor' => '#EC4899'
                ]
            ]
        ];

        return response()->json(['success' => true, 'data' => $data]);
    }

    public function villageRanking()
    {
        $data = [
            'labels' => ['Desa A', 'Desa B', 'Desa C', 'Desa D', 'Desa E'],
            'datasets' => [
                [
                    'label' => 'Skor',
                    'data' => [85, 78, 72, 68, 65],
                    'backgroundColor' => '#8B5CF6'
                ]
            ]
        ];

        return response()->json(['success' => true, 'data' => $data]);
    }
}
