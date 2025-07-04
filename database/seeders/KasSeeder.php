<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Kas;
use App\Models\Penduduk;
use App\Models\Rt;
use App\Models\PengaturanKas;
use Carbon\Carbon;

class KasSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Get all active penduduk with RT data
        $penduduks = Penduduk::whereHas('kk.rt', function($query) {
            $query->where('status', 'aktif');
        })->where('status', 'aktif')->get();

        $currentYear = now()->year;
        $currentWeek = now()->weekOfYear;

        foreach ($penduduks as $penduduk) {
            $rt = $penduduk->kk->rt;
            $pengaturan = PengaturanKas::where('rt_id', $rt->id)->first();

            if ($pengaturan) {
                // Create kas for the last 4 weeks
                for ($week = max(1, $currentWeek - 3); $week <= $currentWeek; $week++) {
                    $dueDate = Carbon::now()->setISODate($currentYear, $week, 0); // Sunday of that week
                    
                    $status = 'belum_bayar';
                    $tanggalBayar = null;
                    $metodeBayar = null;

                    // Randomly mark some as paid
                    if ($week < $currentWeek && rand(1, 100) <= 70) { // 70% chance of being paid for past weeks
                        $status = 'lunas';
                        $tanggalBayar = $dueDate->copy()->addDays(rand(0, 6));
                        $metodeBayar = collect(['tunai', 'transfer', 'digital'])->random();
                    } elseif ($week < $currentWeek && rand(1, 100) <= 20) { // 20% chance of being late
                        $status = 'terlambat';
                    }

                    Kas::create([
                        'penduduk_id' => $penduduk->id,
                        'rt_id' => $rt->id,
                        'rw_id' => $rt->rw_id,
                        'minggu_ke' => $week,
                        'tahun' => $currentYear,
                        'jumlah' => $pengaturan->jumlah_per_minggu,
                        'tanggal_jatuh_tempo' => $dueDate,
                        'tanggal_bayar' => $tanggalBayar,
                        'status' => $status,
                        'metode_bayar' => $metodeBayar,
                        'keterangan' => $status === 'lunas' ? 'Pembayaran rutin mingguan' : null,
                    ]);
                }
            }
        }
    }
}
