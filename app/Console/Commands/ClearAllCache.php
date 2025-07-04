<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ClearAllCache extends Command
{
    protected $signature = 'cache:clear-all';
    protected $description = 'Clear all application caches';

    public function handle()
    {
        $this->info('🧹 Clearing all caches...');
        
        // Clear application cache
        Artisan::call('cache:clear');
        $this->info('✅ Application cache cleared');
        
        // Clear route cache
        Artisan::call('route:clear');
        $this->info('✅ Route cache cleared');
        
        // Clear config cache
        Artisan::call('config:clear');
        $this->info('✅ Config cache cleared');
        
        // Clear view cache
        Artisan::call('view:clear');
        $this->info('✅ View cache cleared');
        
        // Clear compiled services
        if (file_exists(base_path('bootstrap/cache/services.php'))) {
            unlink(base_path('bootstrap/cache/services.php'));
            $this->info('✅ Compiled services cleared');
        }
        
        // Clear compiled packages
        if (file_exists(base_path('bootstrap/cache/packages.php'))) {
            unlink(base_path('bootstrap/cache/packages.php'));
            $this->info('✅ Compiled packages cleared');
        }
        
        $this->info('🎉 All caches cleared successfully!');
        
        return 0;
    }
}
