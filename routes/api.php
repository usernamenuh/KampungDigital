<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Remove any default middleware and rate limiting
Route::middleware([])->group(function () {
    
    // Test endpoint to check if API is working
    Route::get('/test', function () {
        return response()->json([
            'success' => true,
            'message' => 'API is working!',
            'timestamp' => now()->toISOString(),
            'laravel_version' => app()->version()
        ]);
    });

    // Dashboard API Routes - No authentication required for testing
    Route::prefix('dashboard')->group(function () {
        
        // Stats endpoint
        Route::get('/stats', function () {
            try {
                $stats = [
                    [
                        'title' => 'Total Saldo',
                        'value' => 'Rp ' . number_format(15000000 + rand(-500000, 1000000)),
                        'change' => '+' . number_format(rand(8, 15), 1) . '%',
                        'changeType' => 'positive',
                        'icon' => 'wallet',
                        'iconColor' => 'text-emerald-600',
                        'description' => 'Dana bantuan',
                        'subCards' => [
                            ['label' => 'Kas Desa', 'value' => 'Rp ' . number_format(12500000 + rand(-200000, 500000))],
                            ['label' => 'Dana Bantuan', 'value' => 'Rp ' . number_format(2500000 + rand(-100000, 200000))]
                        ]
                    ],
                    [
                        'title' => 'Total Penduduk',
                        'value' => number_format(2430 + rand(-5, 10)),
                        'change' => '+' . number_format(rand(1, 3), 1) . '%',
                        'changeType' => 'positive',
                        'icon' => 'users',
                        'iconColor' => 'text-blue-600',
                        'description' => 'Jiwa terdaftar',
                        'subCards' => [
                            ['label' => 'Laki-laki', 'value' => number_format(1250 + rand(-3, 5))],
                            ['label' => 'Perempuan', 'value' => number_format(1180 + rand(-3, 5))]
                        ]
                    ],
                    [
                        'title' => 'UMKM Aktif',
                        'value' => number_format(45 + rand(-2, 3)),
                        'change' => '+' . number_format(rand(5, 12), 1) . '%',
                        'changeType' => 'positive',
                        'icon' => 'store',
                        'iconColor' => 'text-purple-600',
                        'description' => 'Usaha terdaftar'
                    ],
                    [
                        'title' => 'Tempat Wisata',
                        'value' => number_format(12 + rand(0, 2)),
                        'change' => '+' . number_format(rand(10, 20), 1) . '%',
                        'changeType' => 'positive',
                        'icon' => 'map-pin',
                        'iconColor' => 'text-orange-600',
                        'description' => 'Destinasi aktif'
                    ],
                    [
                        'title' => 'RT & RW',
                        'value' => '30',
                        'change' => 'Stabil',
                        'changeType' => 'stable',
                        'icon' => 'map',
                        'iconColor' => 'text-cyan-600',
                        'description' => 'RT & RW',
                        'subCards' => [
                            ['label' => 'RT', 'value' => '18'],
                            ['label' => 'RW', 'value' => '12']
                        ]
                    ],
                    [
                        'title' => 'Lembaga Pendidikan',
                        'value' => '8',
                        'change' => 'Stabil',
                        'changeType' => 'stable',
                        'icon' => 'graduation-cap',
                        'iconColor' => 'text-indigo-600',
                        'description' => 'Sekolah & kampus'
                    ]
                ];

                return response()->json([
                    'success' => true,
                    'data' => $stats,
                    'timestamp' => now()->toISOString(),
                    'next_update' => now()->addSeconds(30)->toISOString()
                ], 200, [
                    'Content-Type' => 'application/json',
                    'Access-Control-Allow-Origin' => '*',
                    'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS',
                    'Access-Control-Allow-Headers' => 'Content-Type, Authorization, X-Requested-With'
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'error' => 'Internal server error',
                    'message' => $e->getMessage()
                ], 500);
            }
        });

        // Gender data endpoint
        Route::get('/gender-data', function () {
            try {
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
                ], 200, [
                    'Content-Type' => 'application/json',
                    'Access-Control-Allow-Origin' => '*'
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'error' => 'Internal server error'
                ], 500);
            }
        });

        // Monthly data endpoint
        Route::get('/monthly-data', function () {
            try {
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
                ], 200, [
                    'Content-Type' => 'application/json',
                    'Access-Control-Allow-Origin' => '*'
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'error' => 'Internal server error'
                ], 500);
            }
        });

        // Revenue data endpoint
        Route::get('/revenue-data', function () {
            try {
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
                ], 200, [
                    'Content-Type' => 'application/json',
                    'Access-Control-Allow-Origin' => '*'
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'error' => 'Internal server error'
                ], 500);
            }
        });

        // Category data endpoint
        Route::get('/category-data', function () {
            try {
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
                ], 200, [
                    'Content-Type' => 'application/json',
                    'Access-Control-Allow-Origin' => '*'
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'error' => 'Internal server error'
                ], 500);
            }
        });

        // Population trend endpoint
        Route::get('/population-trend', function () {
            try {
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
                ], 200, [
                    'Content-Type' => 'application/json',
                    'Access-Control-Allow-Origin' => '*'
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'error' => 'Internal server error'
                ], 500);
            }
        });

        // Age distribution endpoint
        Route::get('/age-distribution', function () {
            try {
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
                ], 200, [
                    'Content-Type' => 'application/json',
                    'Access-Control-Allow-Origin' => '*'
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'error' => 'Internal server error'
                ], 500);
            }
        });

        // Village ranking endpoint
        Route::get('/village-ranking', function () {
            try {
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
                ], 200, [
                    'Content-Type' => 'application/json',
                    'Access-Control-Allow-Origin' => '*'
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'error' => 'Internal server error'
                ], 500);
            }
        });

        // Activities endpoint
        Route::get('/activities', function () {
            try {
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

                $times = [
                    '1 menit lalu', '2 menit lalu', '5 menit lalu', '10 menit lalu',
                    '15 menit lalu', '30 menit lalu', '1 jam lalu', '2 jam lalu'
                ];

                $randomActivities = [];
                for ($i = 0; $i < 4; $i++) {
                    $randomActivities[] = [
                        'action' => $activities[array_rand($activities)],
                        'time' => $times[array_rand($times)],
                        'type' => ['user', 'business', 'money', 'report'][array_rand(['user', 'business', 'money', 'report'])]
                    ];
                }

                return response()->json([
                    'success' => true,
                    'data' => $randomActivities,
                    'timestamp' => now()->toISOString()
                ], 200, [
                    'Content-Type' => 'application/json',
                    'Access-Control-Allow-Origin' => '*'
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'error' => 'Internal server error'
                ], 500);
            }
        });
    });
});
