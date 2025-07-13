<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PengaturanKas;

class PengaturanKasSeeder extends Seeder
{
    public function run()
    {
        PengaturanKas::create([
            'jumlah_kas_mingguan' => 10000,
            'batas_hari_pembayaran' => 7,
            'persentase_denda' => 2.0,
            'metode_pembayaran_aktif' => json_encode(['tunai', 'bank_transfer', 'e_wallet', 'qr_code']),
            'notifikasi_h_minus' => 3,
            'auto_generate_kas' => true,
            'tahun_aktif' => date('Y'),
            'keterangan' => 'Pengaturan kas default untuk sistem'
        ]);
    }
}
