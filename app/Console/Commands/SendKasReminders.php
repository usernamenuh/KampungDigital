<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Kas;
use App\Models\Notifikasi;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SendKasReminders extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'kas:send-reminders {--days=3}';

    /**
     * The console command description.
     */
    protected $description = 'Send reminders for kas that are due soon';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('📢 Sending kas reminders...');

        try {
            DB::beginTransaction();

            $reminderDays = $this->option('days');
            $reminderDate = now()->addDays($reminderDays);

            // Find kas that are due within reminder days and not yet paid
            $upcomingKas = Kas::where('status', 'belum_bayar')
                ->whereDate('tanggal_jatuh_tempo', '<=', $reminderDate->toDateString())
                ->whereDate('tanggal_jatuh_tempo', '>=', now()->toDateString())
                ->with(['penduduk.user'])
                ->get();

            if ($upcomingKas->isEmpty()) {
                $this->info('✅ No kas reminders to send');
                DB::commit();
                return 0;
            }

            $reminderCount = 0;

            foreach ($upcomingKas as $kas) {
                // Check if reminder already sent today
                $existingReminder = Notifikasi::where('user_id', $kas->penduduk->user->id ?? 0)
                    ->where('kategori', 'kas_reminder')
                    ->whereDate('created_at', now()->toDateString())
                    ->where('data', 'like', '%"kas_id":' . $kas->id . '%')
                    ->first();

                if ($existingReminder) {
                    continue;
                }

                // Send reminder notification
                if ($kas->penduduk->user && $kas->penduduk->user->status === 'aktif') {
                    $daysUntilDue = now()->diffInDays($kas->tanggal_jatuh_tempo);
                    
                    Notifikasi::create([
                        'user_id' => $kas->penduduk->user->id,
                        'judul' => 'Pengingat Kas',
                        'pesan' => "Kas minggu ke-{$kas->minggu_ke} akan jatuh tempo dalam {$daysUntilDue} hari ({$kas->tanggal_jatuh_tempo->format('d/m/Y')}). Jumlah: Rp " . number_format($kas->jumlah, 0, ',', '.'),
                        'tipe' => 'info',
                        'kategori' => 'kas_reminder',
                        'data' => json_encode([
                            'kas_id' => $kas->id,
                            'jumlah' => $kas->jumlah,
                            'minggu_ke' => $kas->minggu_ke,
                            'tahun' => $kas->tahun,
                            'tanggal_jatuh_tempo' => $kas->tanggal_jatuh_tempo->toDateString(),
                            'days_until_due' => $daysUntilDue,
                        ])
                    ]);

                    $reminderCount++;
                    $this->info("📧 Sent reminder to {$kas->penduduk->nama_lengkap} for kas minggu ke-{$kas->minggu_ke}");
                }
            }

            DB::commit();

            $this->info("🎉 Kas reminders sent!");
            $this->info("📊 Total reminders sent: {$reminderCount}");

            return 0;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("❌ Error sending kas reminders: " . $e->getMessage());
            return 1;
        }
    }
}
