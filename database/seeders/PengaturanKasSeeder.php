<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PengaturanKas;
use App\Models\Rt;

class PengaturanKasSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Get all active RTs
        $rts = Rt::where('status', 'aktif')->get();

        foreach ($rts as $rt) {
            PengaturanKas::create([
                'rt_id' => $rt->id,
                'jumlah_per_minggu' => 10000, // Default Rp 10,000 per week
                'hari_bayar' => 'minggu',
                'batas_hari_terlambat' => 7,
                'auto_generate' => true,
                'kirim_pengingat' => true,
                'hari_pengingat_sebelum' => 2,
                'hari_pengingat_setelah' => 3,
                'aktif' => true,
            ]);
        }
    }
}
