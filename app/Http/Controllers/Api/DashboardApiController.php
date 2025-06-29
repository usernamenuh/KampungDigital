<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Desa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

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
            $currentDate = Carbon::now()->locale('id');
            
            $stats = [
                [
                    'title' => 'Total Desa',
                    'value' => (string)max($totalDesa, 12),
                    'change' => '+2.5%',
                    'changeType' => 'positive',
                    'icon' => 'building-2',
                    'iconColor' => 'text-blue-600',
                    'description' => 'Desa terdaftar',
                    'subCards' => [
                        ['label' => 'Aktif', 'value' => (string)max($totalDesa, 12)],
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
                    'description' => 'Usaha terdaftar',
                    'subCards' => [
                        ['label' => 'Kuliner', 'value' => '18'],
                        ['label' => 'Kerajinan', 'value' => '26']
                    ]
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
                    'description' => 'Program berjalan',
                    'subCards' => [
                        ['label' => 'Pembangunan', 'value' => '8'],
                        ['label' => 'Sosial', 'value' => '5']
                    ]
                ],
                [
                    'title' => 'Berita',
                    'value' => '28',
                    'change' => '+8.1%',
                    'changeType' => 'positive',
                    'icon' => 'newspaper',
                    'iconColor' => 'text-indigo-600',
                    'description' => 'Berita terpublikasi',
                    'subCards' => [
                        ['label' => 'Bulan ini', 'value' => '12'],
                        ['label' => 'Minggu ini', 'value' => '4']
                    ]
                ],
                [
                    'title' => 'Wisata',
                    'value' => '8',
                    'change' => '+12.5%',
                    'changeType' => 'positive',
                    'icon' => 'camera',
                    'iconColor' => 'text-pink-600',
                    'description' => 'Destinasi wisata',
                    'subCards' => [
                        ['label' => 'Alam', 'value' => '5'],
                        ['label' => 'Budaya', 'value' => '3']
                    ]
                ],
                [
                    'title' => 'Dokumen',
                    'value' => '156',
                    'change' => '+3.2%',
                    'changeType' => 'positive',
                    'icon' => 'file-text',
                    'iconColor' => 'text-cyan-600',
                    'description' => 'Dokumen tersimpan',
                    'subCards' => [
                        ['label' => 'Surat', 'value' => '89'],
                        ['label' => 'Laporan', 'value' => '67']
                    ]
                ],
                [
                    'title' => 'Pesan',
                    'value' => '24',
                    'change' => '+15.2%',
                    'changeType' => 'positive',
                    'icon' => 'message-circle',
                    'iconColor' => 'text-yellow-600',
                    'description' => 'Pesan masuk',
                    'subCards' => [
                        ['label' => 'Belum dibaca', 'value' => '8'],
                        ['label' => 'Sudah dibaca', 'value' => '16']
                    ]
                ]
            ];

            Log::info('Stats prepared: ' . count($stats) . ' items');

            return response()->json([
                'success' => true,
                'data' => $stats,
                'timestamp' => now()->toISOString(),
                'current_date' => $currentDate->isoFormat('dddd, D MMMM YYYY'),
                'current_time' => $currentDate->format('H:i:s'),
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
            ['action' => 'Berita baru dipublikasi', 'time' => '3 jam yang lalu'],
            ['action' => 'Dokumen surat masuk', 'time' => '4 jam yang lalu'],
            ['action' => 'Pesan dari warga diterima', 'time' => '5 jam yang lalu']
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
                    'fill' => true,
                    'tension' => 0.4
                ],
                [
                    'label' => 'UMKM',
                    'data' => [35, 38, 40, 42, 43, 44],
                    'borderColor' => '#10B981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                    'tension' => 0.4
                ]
            ]
        ];

        return response()->json(['success' => true, 'data' => $data]);
    }

    public function revenueData()
    {
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'];
        $data = [
            'labels' => $months,
            'datasets' => [
                [
                    'label' => 'Pendapatan (Juta Rupiah)',
                    'data' => [15, 18, 22, 25, 28, 32],
                    'backgroundColor' => [
                        '#8B5CF6', '#A855F7', '#C084FC', 
                        '#D8B4FE', '#E9D5FF', '#F3E8FF'
                    ],
                    'borderColor' => '#8B5CF6',
                    'borderWidth' => 1
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
                    'backgroundColor' => [
                        '#3B82F6', '#10B981', '#F59E0B', 
                        '#EF4444', '#8B5CF6'
                    ],
                    'borderWidth' => 0
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
                    'fill' => true,
                    'tension' => 0.4
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
            'labels' => ['Desa Sukamaju', 'Desa Makmur', 'Desa Sejahtera', 'Desa Merdeka', 'Desa Harmoni'],
            'datasets' => [
                [
                    'label' => 'Skor Pembangunan',
                    'data' => [85, 78, 72, 68, 65],
                    'backgroundColor' => [
                        '#10B981', '#3B82F6', '#F59E0B', 
                        '#EF4444', '#8B5CF6'
                    ],
                    'borderWidth' => 0
                ]
            ]
        ];

        return response()->json(['success' => true, 'data' => $data]);
    }
}
