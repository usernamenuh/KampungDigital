<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function index()
    {
        // Data yang bisa Anda pass ke view jika diperlukan
        $data = [
            'totalSaldo' => 15000000,
            'totalPenduduk' => 2430,
            'umkmAktif' => 45,
            'tempatWisata' => 12,
            'activities' => [
                ['action' => 'Penduduk baru terdaftar', 'time' => '2 menit lalu'],
                ['action' => 'UMKM baru disetujui', 'time' => '15 menit lalu'],
                ['action' => 'Dana bantuan dicairkan', 'time' => '1 jam lalu'],
                ['action' => 'Laporan bulanan dibuat', 'time' => '2 jam lalu'],
            ]
        ];

        return view('dashboard', compact('data'));
    }



    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */


    private function getTotalSaldo()
    {
        // Contoh: return DB::table('saldo')->sum('jumlah');
        return 15000000; // Contoh data
    }

    private function getJumlahRT()
    {
        // Contoh: return DB::table('rt')->count();
        return 25; // Contoh data
    }

    private function getJumlahRW()
    {
        // Contoh: return DB::table('rw')->count();
        return 5; // Contoh data
    }

    private function getJumlahTempatWisata()
    {
        // Contoh: return DB::table('tempat_wisata')->count();
        return 12; // Contoh data
    }

    private function getJumlahPendidikan()
    {
        // Contoh: return DB::table('lembaga_pendidikan')->count();
        return 8; // Contoh data
    }

    private function getJumlahUMKM()
    {
        // Contoh: return DB::table('umkm')->count();
        return 45; // Contoh data
    }

    private function getJumlahLakiLaki()
    {
        // Contoh: return DB::table('penduduk')->where('jenis_kelamin', 'L')->count();
        return 1250; // Contoh data
    }

    private function getJumlahPerempuan()
    {
        // Contoh: return DB::table('penduduk')->where('jenis_kelamin', 'P')->count();
        return 1180; // Contoh data
    }
}