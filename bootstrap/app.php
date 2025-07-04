<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Register middleware aliases
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleCheck::class,
            'user.status' => \App\Http\Middleware\CheckUserStatus::class,
        ]);
        
        // Global middleware untuk web routes
        $middleware->web(append: [
            \App\Http\Middleware\UpdateLastActivity::class,
        ]);
    })
    ->withSchedule(function (Schedule $schedule) {
        // Generate weekly kas every Monday at 6 AM
        $schedule->command('kas:generate-weekly')
                 ->weeklyOn(1, '06:00')
                 ->description('Generate weekly kas for all RT')
                 ->withoutOverlapping()
                 ->runInBackground();

        // Update kas status daily at 1 AM
        $schedule->command('kas:update-status')
                 ->dailyAt('01:00')
                 ->description('Update overdue kas status')
                 ->withoutOverlapping();

        // Send kas reminders daily at 8 AM
        $schedule->command('kas:send-reminders --days=3')
                 ->dailyAt('08:00')
                 ->description('Send kas payment reminders')
                 ->withoutOverlapping();

        // Send urgent reminders for tomorrow's due kas at 6 PM
        $schedule->command('kas:send-reminders --days=1')
                 ->dailyAt('18:00')
                 ->description('Send urgent kas reminders')
                 ->withoutOverlapping();

        // Cleanup old notifications weekly on Sunday at 2 AM
        $schedule->command('notifications:cleanup --days=30')
                 ->weeklyOn(0, '02:00')
                 ->description('Cleanup old notifications')
                 ->withoutOverlapping();

        // Update user online status every 5 minutes
        $schedule->call(function () {
            try {
                // Mark users as offline if they haven't been active for more than 10 minutes
                \Illuminate\Support\Facades\DB::table('users')
                    ->where('last_activity', '<', now()->subMinutes(10))
                    ->whereNotNull('last_activity')
                    ->update(['updated_at' => now()]);
                    
                \Log::info('User online status updated');
            } catch (\Exception $e) {
                \Log::warning('Failed to update user online status', [
                    'error' => $e->getMessage()
                ]);
            }
        })->everyFiveMinutes()
          ->description('Update user online status')
          ->withoutOverlapping();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Handle BadMethodCallException (missing methods)
        $exceptions->render(function (\BadMethodCallException $e, $request) {
            \Log::error('BadMethodCallException occurred', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'url' => $request->fullUrl(),
                'user_id' => auth()->id()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Method tidak ditemukan'
                ], 500);
            }

            // Return a simple error response instead of trying to render a view
            return response('<h1>500 - Internal Server Error</h1><p>Terjadi kesalahan pada server. Silakan coba lagi nanti.</p>', 500)
                ->header('Content-Type', 'text/html');
        });

        // Custom exception handling untuk API responses
        $exceptions->render(function (\Illuminate\Database\Eloquent\ModelNotFoundException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan'
                ], 404);
            }
        });

        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized - Silakan login terlebih dahulu'
                ], 401);
            }
        });

        $exceptions->render(function (\Illuminate\Auth\Access\AuthorizationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Forbidden - Anda tidak memiliki akses'
                ], 403);
            }
        });
    })->create();