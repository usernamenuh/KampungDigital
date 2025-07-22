<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class KkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil semua RT yang ada
        $rtIds = DB::table('rts')->pluck('id');

        if ($rtIds->isEmpty()) {
            $this->command->warn('Tidak ada data RT, seeder KK dilewati.');
            return;
        }

        foreach (range(1, 20) as $i) {
            DB::table('kks')->insert([
                'no_kk' => $this->generateNoKk($i),
                'rt_id' => $rtIds->random(),
                'alamat' => 'Jalan Contoh No. ' . $i,
                'status' => collect(['aktif', 'tidak_aktif'])->random(),
                'tanggal_dibuat' => Carbon::now()->subDays(rand(0, 365)),
                'keterangan' => rand(0, 1) ? 'Keluarga baru pindahan.' : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function generateNoKk($i): string
    {
        // Contoh: 327312150399XXXX (acak 4 digit di akhir)
        $base = '327312' . date('dmy', strtotime('1999-03-15'));
        return $base . str_pad($i, 4, '0', STR_PAD_LEFT); // max 9999 entry
    }
}
