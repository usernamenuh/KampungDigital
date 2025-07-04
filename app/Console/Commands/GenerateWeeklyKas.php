<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Kas;
use App\Models\Penduduk;
use App\Models\Rt;
use App\Models\Notifikasi;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GenerateWeeklyKas extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'kas:generate-weekly {--rt_id=} {--jumlah=10000} {--force}';

    /**
     * The console command description.
     */
    protected $description = 'Generate weekly kas for all residents in RT';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting weekly kas generation...');

        try {
            DB::beginTransaction();

            $rtId = $this->option('rt_id');
            $jumlah = $this->option('jumlah');
            $force = $this->option('force');

            $currentWeek = now()->weekOfYear;
            $currentYear = now()->year;
            $jatuhTempo = now()->addDays(7);

            // Get RT list
            $rtList = $rtId ? Rt::where('id', $rtId)->get() : Rt::all();

            if ($rtList->isEmpty()) {
                $this->error('❌ No RT found');
                return 1;
            }

            $totalCreated = 0;
            $totalNotifications = 0;

            foreach ($rtList as $rt) {
                $this->info("📍 Processing RT {$rt->no_rt}...");

                // Get active residents in this RT
                $pendudukList = Penduduk::whereHas('kk', function($query) use ($rt) {
                    $query->where('rt_id', $rt->id);
                })->where('status', 'aktif')->get();

                if ($pendudukList->isEmpty()) {
                    $this->warn("⚠️  No active residents found in RT {$rt->no_rt}");
                    continue;
                }

                $createdForRt = 0;

                foreach ($pendudukList as $penduduk) {
                    // Check if kas already exists for this week
                    $existingKas = Kas::where('penduduk_id', $penduduk->id)
                        ->where('minggu_ke', $currentWeek)
                        ->where('tahun', $currentYear)
                        ->first();

                    if ($existingKas && !$force) {
                        continue;
                    }

                    if ($existingKas && $force) {
                        $existingKas->delete();
                    }

                    // Create new kas
                    $kas = Kas::create([
                        'penduduk_id' => $penduduk->id,
                        'rt_id' => $rt->id,
                        'rw_id' => $rt->rw_id,
                        'minggu_ke' => $currentWeek,
                        'tahun' => $currentYear,
                        'jumlah' => $jumlah,
                        'tanggal_jatuh_tempo' => $jatuhTempo,
                        'status' => 'belum_bayar',
                        'keterangan' => 'Generated automatically by system',
                        'dibuat_oleh' => 1, // System user
                    ]);

                    $createdForRt++;
                    $totalCreated++;

                    // Send notification to resident if they have user account
                    if ($penduduk->user && $penduduk->user->status === 'aktif') {
                        Notifikasi::create([
                            'user_id' => $penduduk->user->id,
                            'judul' => 'Tagihan Kas Baru',
                            'pesan' => "Tagihan kas minggu ke-{$currentWeek} sebesar Rp " . number_format($jumlah, 0, ',', '.') . " telah dibuat. Jatuh tempo: " . $jatuhTempo->format('d/m/Y'),
                            'tipe' => 'info',
                            'kategori' => 'kas',
                            'data' => json_encode([
                                'kas_id' => $kas->id,
                                'jumlah' => $jumlah,
                                'minggu_ke' => $currentWeek,
                                'tahun' => $currentYear,
                                'tanggal_jatuh_tempo' => $jatuhTempo->toDateString(),
                            ])
                        ]);

                        $totalNotifications++;
                    }
                }

                $this->info("✅ Created {$createdForRt} kas entries for RT {$rt->no_rt}");
            }

            // Send summary notification to admins
            $adminUsers = User::whereIn('role', ['admin', 'kades'])
                ->where('status', 'aktif')
                ->get();

            foreach ($adminUsers as $admin) {
                Notifikasi::create([
                    'user_id' => $admin->id,
                    'judul' => 'Weekly Kas Generation Complete',
                    'pesan' => "Berhasil generate {$totalCreated} tagihan kas minggu ke-{$currentWeek}. Total notifikasi dikirim: {$totalNotifications}",
                    'tipe' => 'success',
                    'kategori' => 'system',
                    'data' => json_encode([
                        'total_created' => $totalCreated,
                        'total_notifications' => $totalNotifications,
                        'minggu_ke' => $currentWeek,
                        'tahun' => $currentYear,
                    ])
                ]);
            }

            DB::commit();

            $this->info("🎉 Weekly kas generation completed!");
            $this->info("📊 Total kas created: {$totalCreated}");
            $this->info("📧 Total notifications sent: {$totalNotifications}");

            return 0;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("❌ Error generating weekly kas: " . $e->getMessage());
            return 1;
        }
    }
}
