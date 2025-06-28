<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // ✅ TAMBAHKAN INI - Daftarkan middleware role
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleCheck::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Basic exception handling
        $exceptions->render(function (Throwable $e) {
            if (app()->environment('local')) {
                return response()->json([
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ], 500);
            }
        });
    })->create();