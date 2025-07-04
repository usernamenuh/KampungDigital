<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Kas;
use App\Models\Notifikasi;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UpdateKasStatus extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'kas:update-status';

    /**
     * The console command description.
     */
    protected $description = 'Update kas status to terlambat for overdue payments';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Updating kas status...');

        try {
            DB::beginTransaction();

            // Find kas that are overdue but not yet marked as terlambat
            $overdueKas = Kas::where('status', 'belum_bayar')
                ->where('tanggal_jatuh_tempo', '<', now())
                ->get();

            if ($overdueKas->isEmpty()) {
                $this->info('✅ No overdue kas found');
                DB::commit();
                return 0;
            }

            $updatedCount = 0;
            $notificationCount = 0;

            foreach ($overdueKas as $kas) {
                // Update status to terlambat
                $kas->update(['status' => 'terlambat']);
                $updatedCount++;

                // Send notification to resident
                if ($kas->penduduk->user && $kas->penduduk->user->status === 'aktif') {
                    Notifikasi::create([
                        'user_id' => $kas->penduduk->user->id,
                        'judul' => 'Kas Terlambat',
                        'pesan' => "Kas minggu ke-{$kas->minggu_ke} telah melewati jatuh tempo ({$kas->tanggal_jatuh_tempo->format('d/m/Y')}). Segera lakukan pembayaran untuk menghindari denda.",
                        'tipe' => 'warning',
                        'kategori' => 'kas',
                        'data' => json_encode([
                            'kas_id' => $kas->id,
                            'jumlah' => $kas->jumlah,
                            'minggu_ke' => $kas->minggu_ke,
                            'tahun' => $kas->tahun,
                            'hari_terlambat' => $kas->tanggal_jatuh_tempo->diffInDays(now()),
                        ])
                    ]);

                    $notificationCount++;
                }

                $this->info("⚠️  Updated kas ID {$kas->id} for {$kas->penduduk->nama_lengkap} to terlambat");
            }

            DB::commit();

            $this->info("🎉 Kas status update completed!");
            $this->info("📊 Total kas updated: {$updatedCount}");
            $this->info("📧 Total notifications sent: {$notificationCount}");

            return 0;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("❌ Error updating kas status: " . $e->getMessage());
            return 1;
        }
    }
}
