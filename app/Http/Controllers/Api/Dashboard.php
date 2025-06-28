<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Dashboard extends Controller
{
    public function getStats()
    {
        // Simulate real-time data with some randomization
        $baseStats = [
            'totalSaldo' => 15000000,
            'totalPenduduk' => 2430,
            'umkmAktif' => 45,
            'tempatWisata' => 12,
            'rtRw' => 30,
            'lembagaPendidikan' => 8
        ];

        // Add some realistic fluctuation
        $stats = [
            [
                'title' => 'Total Saldo',
                'value' => 'Rp ' . number_format($baseStats['totalSaldo'] + rand(-500000, 1000000)),
                'change' => '+' . number_format(rand(8, 15), 1) . '%',
                'changeType' => 'positive',
                'icon' => 'wallet',
                'iconBg' => 'bg-emerald-500',
                'cardBg' => 'from-emerald-500 to-emerald-600',
                'description' => 'Dana bantuan',
                'subCards' => [
                    ['label' => 'Kas Desa', 'value' => 'Rp ' . number_format(12500000 + rand(-200000, 500000))],
                    ['label' => 'Dana Bantuan', 'value' => 'Rp ' . number_format(2500000 + rand(-100000, 200000))]
                ]
            ],
            [
                'title' => 'Total Penduduk',
                'value' => number_format($baseStats['totalPenduduk'] + rand(-5, 10)),
                'change' => '+' . number_format(rand(1, 3), 1) . '%',
                'changeType' => 'positive',
                'icon' => 'users',
                'iconBg' => 'bg-blue-500',
                'cardBg' => 'from-blue-500 to-blue-600',
                'description' => 'Jiwa terdaftar',
                'subCards' => [
                    ['label' => 'Laki-laki', 'value' => number_format(1250 + rand(-3, 5))],
                    ['label' => 'Perempuan', 'value' => number_format(1180 + rand(-3, 5))]
                ]
            ],
            [
                'title' => 'UMKM Aktif',
                'value' => number_format($baseStats['umkmAktif'] + rand(-2, 3)),
                'change' => '+' . number_format(rand(5, 12), 1) . '%',
                'changeType' => 'positive',
                'icon' => 'store',
                'iconBg' => 'bg-purple-500',
                'cardBg' => 'from-purple-500 to-purple-600',
                'description' => 'Usaha terdaftar'
            ],
            [
                'title' => 'Tempat Wisata',
                'value' => number_format($baseStats['tempatWisata'] + rand(0, 2)),
                'change' => '+' . number_format(rand(10, 20), 1) . '%',
                'changeType' => 'positive',
                'icon' => 'map-pin',
                'iconBg' => 'bg-orange-500',
                'cardBg' => 'from-orange-500 to-orange-600',
                'description' => 'Destinasi aktif'
            ],
            [
                'title' => 'RT & RW',
                'value' => number_format($baseStats['rtRw']),
                'change' => 'Stabil',
                'changeType' => 'stable',
                'icon' => 'map',
                'iconBg' => 'bg-cyan-500',
                'cardBg' => 'from-cyan-500 to-cyan-600',
                'description' => 'RT & RW',
                'subCards' => [
                    ['label' => 'RT', 'value' => '18'],
                    ['label' => 'RW', 'value' => '12']
                ]
            ],
            [
                'title' => 'Lembaga Pendidikan',
                'value' => number_format($baseStats['lembagaPendidikan']),
                'change' => 'Stabil',
                'changeType' => 'stable',
                'icon' => 'graduation-cap',
                'iconBg' => 'bg-indigo-500',
                'cardBg' => 'from-indigo-500 to-indigo-600',
                'description' => 'Sekolah & kampus'
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
            'timestamp' => now()->toISOString(),
            'next_update' => now()->addSeconds(30)->toISOString()
        ]);
    }

    public function getMonthlyData()
    {
        // Generate dynamic monthly data
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
        $baseData = [
            'penduduk' => [2300, 2350, 2380, 2400, 2420, 2430],
            'umkm' => [35, 38, 40, 42, 44, 45],
            'pendapatan' => [12, 13, 14, 14.5, 14.8, 15],
            'pengeluaran' => [7, 7.5, 8, 8.2, 8.3, 8.5]
        ];

        // Add some real-time variation
        foreach ($baseData as $key => &$values) {
            foreach ($values as &$value) {
                if ($key === 'penduduk') {
                    $value += rand(-5, 10);
                } elseif ($key === 'umkm') {
                    $value += rand(-1, 2);
                } else {
                    $value += rand(-50, 100) / 100;
                }
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'labels' => $months,
                'datasets' => [
                    [
                        'label' => 'Penduduk',
                        'data' => $baseData['penduduk'],
                        'borderColor' => '#8B5CF6',
                        'backgroundColor' => 'rgba(139, 92, 246, 0.1)',
                        'tension' => 0.4,
                        'fill' => true
                    ],
                    [
                        'label' => 'UMKM',
                        'data' => $baseData['umkm'],
                        'borderColor' => '#06B6D4',
                        'backgroundColor' => 'rgba(6, 182, 212, 0.1)',
                        'tension' => 0.4,
                        'fill' => true
                    ],
                    [
                        'label' => 'Pendapatan (Juta)',
                        'data' => $baseData['pendapatan'],
                        'borderColor' => '#10B981',
                        'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                        'tension' => 0.4,
                        'fill' => true
                    ],
                    [
                        'label' => 'Pengeluaran (Juta)',
                        'data' => $baseData['pengeluaran'],
                        'borderColor' => '#EF4444',
                        'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                        'tension' => 0.4,
                        'fill' => true
                    ]
                ]
            ],
            'timestamp' => now()->toISOString()
        ]);
    }

    public function getGenderData()
    {
        $male = 1250 + rand(-5, 10);
        $female = 1180 + rand(-5, 10);
        
        return response()->json([
            'success' => true,
            'data' => [
                'male' => $male,
                'female' => $female,
                'total' => $male + $female
            ],
            'timestamp' => now()->toISOString()
        ]);
    }

    public function getRevenueData()
    {
        $baseRevenue = [12000000, 13500000, 14200000, 14800000, 15200000, 15600000];
        
        // Add variation
        foreach ($baseRevenue as &$value) {
            $value += rand(-500000, 1000000);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                'datasets' => [[
                    'label' => 'Pendapatan Desa',
                    'data' => $baseRevenue,
                    'backgroundColor' => 'rgba(139, 92, 246, 0.8)',
                    'borderColor' => '#8B5CF6',
                    'borderWidth' => 2
                ]]
            ],
            'timestamp' => now()->toISOString()
        ]);
    }

    public function getCategoryData()
    {
        $baseData = [15, 12, 8, 6, 10, 4];
        
        // Add variation
        foreach ($baseData as &$value) {
            $value += rand(-2, 3);
            $value = max(1, $value); // Ensure minimum 1
        }

        return response()->json([
            'success' => true,
            'data' => [
                'labels' => ['UMKM', 'Pertanian', 'Perikanan', 'Kerajinan', 'Kuliner', 'Jasa'],
                'datasets' => [[
                    'label' => 'Jumlah Usaha',
                    'data' => $baseData,
                    'backgroundColor' => [
                        '#8B5CF6', '#06B6D4', '#10B981', '#F59E0B', '#EF4444', '#6366F1'
                    ],
                    'borderWidth' => 0
                ]]
            ],
            'timestamp' => now()->toISOString()
        ]);
    }

    public function getPopulationTrend()
    {
        $baseData = [2200, 2280, 2350, 2380, 2410, 2430];
        
        // Add slight variation
        foreach ($baseData as &$value) {
            $value += rand(-10, 15);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'labels' => ['2019', '2020', '2021', '2022', '2023', '2024'],
                'datasets' => [[
                    'label' => 'Jumlah Penduduk',
                    'data' => $baseData,
                    'backgroundColor' => 'rgba(16, 185, 129, 0.3)',
                    'borderColor' => '#10B981',
                    'borderWidth' => 3,
                    'fill' => true,
                    'tension' => 0.4
                ]]
            ],
            'timestamp' => now()->toISOString()
        ]);
    }

    public function getAgeDistribution()
    {
        $maleBase = [180, 220, 280, 180, 120];
        $femaleBase = [170, 210, 270, 175, 115];
        
        // Add variation
        foreach ($maleBase as &$value) {
            $value += rand(-10, 15);
        }
        foreach ($femaleBase as &$value) {
            $value += rand(-10, 15);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'labels' => ['0-17 Tahun', '18-30 Tahun', '31-45 Tahun', '46-60 Tahun', '60+ Tahun'],
                'datasets' => [
                    [
                        'label' => 'Laki-laki',
                        'data' => $maleBase,
                        'backgroundColor' => '#3B82F6'
                    ],
                    [
                        'label' => 'Perempuan',
                        'data' => $femaleBase,
                        'backgroundColor' => '#EC4899'
                    ]
                ]
            ],
            'timestamp' => now()->toISOString()
        ]);
    }

    public function getVillageRanking()
    {
        $baseScores = [95, 88, 82, 78, 75];
        
        // Add slight variation
        foreach ($baseScores as &$score) {
            $score += rand(-2, 3);
            $score = max(60, min(100, $score)); // Keep between 60-100
        }

        return response()->json([
            'success' => true,
            'data' => [
                'labels' => ['Desa Makmur', 'Desa Sejahtera', 'Desa Maju', 'Desa Berkah', 'Desa Sentosa'],
                'datasets' => [[
                    'label' => 'Skor Pembangunan',
                    'data' => $baseScores,
                    'backgroundColor' => [
                        '#10B981', '#06B6D4', '#8B5CF6', '#F59E0B', '#EF4444'
                    ]
                ]]
            ],
            'timestamp' => now()->toISOString()
        ]);
    }

    public function getActivities()
    {
        $activities = [
            'Penduduk baru terdaftar',
            'UMKM baru disetujui', 
            'Dana bantuan dicairkan',
            'Laporan bulanan dibuat',
            'Surat izin diterbitkan',
            'Program bantuan dimulai',
            'Rapat RT dilaksanakan',
            'Kegiatan posyandu selesai'
        ];

        $randomActivities = [];
        for ($i = 0; $i < 4; $i++) {
            $randomActivities[] = [
                'action' => $activities[array_rand($activities)],
                'time' => $this->getRandomTime(),
                'type' => ['user', 'business', 'money', 'report'][array_rand(['user', 'business', 'money', 'report'])]
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $randomActivities,
            'timestamp' => now()->toISOString()
        ]);
    }

    public function getSystemStatus()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'status' => 'online',
                'server_load' => rand(15, 45) . '%',
                'memory_usage' => rand(35, 65) . '%',
                'active_users' => rand(8, 25),
                'last_backup' => now()->subHours(rand(1, 6))->toISOString(),
                'uptime' => rand(95, 99) . '.9%'
            ],
            'timestamp' => now()->toISOString()
        ]);
    }

    private function getRandomTime()
    {
        $times = [
            '1 menit lalu', '2 menit lalu', '5 menit lalu', '10 menit lalu',
            '15 menit lalu', '30 menit lalu', '1 jam lalu', '2 jam lalu'
        ];
        
        return $times[array_rand($times)];
    }
}
