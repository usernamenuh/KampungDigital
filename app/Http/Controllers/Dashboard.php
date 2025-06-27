<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $data = [
            'totalSaldo' => $this->getTotalSaldo(),
            'jumlahRT' => $this->getJumlahRT(),
            'jumlahRW' => $this->getJumlahRW(),
            'jumlahTempatWisata' => $this->getJumlahTempatWisata(),
            'jumlahPendidikan' => $this->getJumlahPendidikan(),
            'jumlahUMKM' => $this->getJumlahUMKM(),
            'jumlahLakiLaki' => $this->getJumlahLakiLaki(),
            'jumlahPerempuan' => $this->getJumlahPerempuan(),
            'totalPenduduk' => $this->getJumlahLakiLaki() + $this->getJumlahPerempuan(),
            'totalRTRW' => $this->getJumlahRT() + $this->getJumlahRW(),
            'recentNews' => $this->getRecentNews(),
            'activeScholarships' => $this->getActiveScholarships(),
            'monthlyStats' => $this->getMonthlyStats(),
        ];

        return view('dashboard', $data);
    }

    /**
     * Get dashboard data for AJAX requests
     */
    public function getData()
    {
        return response()->json([
            'totalSaldo' => $this->getTotalSaldo(),
            'jumlahRT' => $this->getJumlahRT(),
            'jumlahRW' => $this->getJumlahRW(),
            'jumlahTempatWisata' => $this->getJumlahTempatWisata(),
            'jumlahPendidikan' => $this->getJumlahPendidikan(),
            'jumlahUMKM' => $this->getJumlahUMKM(),
            'jumlahLakiLaki' => $this->getJumlahLakiLaki(),
            'jumlahPerempuan' => $this->getJumlahPerempuan(),
            'monthlyStats' => $this->getMonthlyStats(),
        ]);
    }

    /**
     * Export dashboard data
     */
    public function exportData()
    {
        // Implementation for data export
        return response()->json(['message' => 'Export feature coming soon']);
    }

    // Private methods for data retrieval
    private function getTotalSaldo()
    {
        // Example: return DB::table('saldo')->sum('jumlah');
        return 15000000; // Sample data
    }

    private function getJumlahRT()
    {
        // Example: return DB::table('rt')->count();
        return 25; // Sample data
    }

    private function getJumlahRW()
    {
        // Example: return DB::table('rw')->count();
        return 5; // Sample data
    }

    private function getJumlahTempatWisata()
    {
        // Example: return DB::table('tempat_wisata')->count();
        return 12; // Sample data
    }

    private function getJumlahPendidikan()
    {
        // Example: return DB::table('lembaga_pendidikan')->count();
        return 8; // Sample data
    }

    private function getJumlahUMKM()
    {
        // Example: return DB::table('umkm')->count();
        return 45; // Sample data
    }

    private function getJumlahLakiLaki()
    {
        // Example: return DB::table('penduduk')->where('jenis_kelamin', 'L')->count();
        return 1250; // Sample data
    }

    private function getJumlahPerempuan()
    {
        // Example: return DB::table('penduduk')->where('jenis_kelamin', 'P')->count();
        return 1180; // Sample data
    }

    private function getRecentNews()
    {
        // Example: return DB::table('news')->orderBy('created_at', 'desc')->limit(5)->get();
        return [
            [
                'id' => 1,
                'title' => 'Pembangunan Jalan Desa Tahap 2',
                'category' => 'Pembangunan',
                'date' => '2024-12-15',
                'status' => 'Published',
                'views' => 245
            ],
            [
                'id' => 2,
                'title' => 'Festival Budaya Kampung Digital 2024',
                'category' => 'Kegiatan',
                'date' => '2024-12-12',
                'status' => 'Published',
                'views' => 189
            ]
        ];
    }

    private function getActiveScholarships()
    {
        // Example: return DB::table('scholarships')->where('status', 'active')->get();
        return [
            [
                'id' => 1,
                'name' => 'Beasiswa Pendidikan Tinggi',
                'type' => 'Program S1 & S2',
                'applicants' => 25,
                'max_applicants' => 30,
                'budget' => 150000000,
                'deadline' => '2024-12-31'
            ],
            [
                'id' => 2,
                'name' => 'Beasiswa Prestasi Akademik',
                'type' => 'SMA/SMK Sederajat',
                'applicants' => 18,
                'max_applicants' => 20,
                'budget' => 80000000,
                'deadline' => '2025-01-15'
            ]
        ];
    }

    private function getMonthlyStats()
    {
        // Example: return monthly statistics for charts
        return [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            'penduduk' => [2400, 2405, 2410, 2415, 2420, 2425, 2428, 2430, 2430, 2430, 2430, 2430],
            'umkm' => [35, 37, 39, 41, 42, 43, 44, 44, 45, 45, 45, 45]
        ];
    }
}   