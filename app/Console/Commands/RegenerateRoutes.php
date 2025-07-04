<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class RegenerateRoutes extends Command
{
    protected $signature = 'routes:regenerate';
    protected $description = 'Clear and regenerate all routes';

    public function handle()
    {
        $this->info('🔄 Regenerating routes...');
        
        // Clear route cache
        Artisan::call('route:clear');
        $this->info('✅ Route cache cleared');
        
        // Clear config cache
        Artisan::call('config:clear');
        $this->info('✅ Config cache cleared');
        
        // Cache routes
        Artisan::call('route:cache');
        $this->info('✅ Routes cached');
        
        $this->info('🎉 Routes regenerated successfully!');
        
        return 0;
    }
}
