<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Notifikasi;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CleanupNotifications extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'notifications:cleanup {--days=30}';

    /**
     * The console command description.
     */
    protected $description = 'Clean up old notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧹 Cleaning up old notifications...');

        try {
            DB::beginTransaction();

            $days = $this->option('days');
            $cutoffDate = now()->subDays($days);

            // Delete old read notifications
            $deletedRead = Notifikasi::where('dibaca', true)
                ->where('created_at', '<', $cutoffDate)
                ->delete();

            // Delete very old unread notifications (60 days)
            $deletedUnread = Notifikasi::where('dibaca', false)
                ->where('created_at', '<', now()->subDays(60))
                ->delete();

            DB::commit();

            $this->info("🎉 Notification cleanup completed!");
            $this->info("📊 Deleted read notifications: {$deletedRead}");
            $this->info("📊 Deleted old unread notifications: {$deletedUnread}");

            return 0;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("❌ Error cleaning up notifications: " . $e->getMessage());
            return 1;
        }
    }
}
