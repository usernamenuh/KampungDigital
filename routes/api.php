<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DashboardApiController;
use App\Http\Controllers\Api\KasApiController;
use App\Http\Controllers\Api\PaymentApiController;
use App\Http\Controllers\Api\NotifikasiApiController;
use App\Http\Controllers\Api\AuthApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    // Dashboard API routes - Centralized dashboard logic
    Route::prefix('dashboard')->group(function () {
        Route::get('/stats', [DashboardApiController::class, 'getStats']);
        Route::get('/monthly-kas', [DashboardApiController::class, 'getMonthlyKasData']);
        Route::get('/activities', [DashboardApiController::class, 'getActivities']);
        Route::get('/payment-alerts', [DashboardApiController::class, 'getPaymentAlerts']);
        Route::get('/system-monitoring', [DashboardApiController::class, 'getSystemMonitoring']);
        Route::get('/system-health', [DashboardApiController::class, 'getSystemHealth']);
        Route::post('/clear-cache', [DashboardApiController::class, 'clearCache']);
        Route::post('/update-activity', [DashboardApiController::class, 'updateActivity']);
    });

    // Kas API routes
    Route::prefix('kas')->group(function () {
        Route::get('/stats', [KasApiController::class, 'getStats']);
        Route::get('/user/{userId}', [KasApiController::class, 'getUserKas']);
        Route::get('/recent-payments', [KasApiController::class, 'getRecentPayments']);
    });

    // Payment API routes - Enhanced for masyarakat dashboard
    Route::prefix('payment')->group(function () {
        Route::get('/index', [PaymentApiController::class, 'index']);
        Route::post('/{payment}/confirm', [PaymentApiController::class, 'confirmPayment']);
    });

    // Payment Info API routes
    Route::prefix('payment-info')->group(function () {
        Route::get('/for-user-rt', [PaymentApiController::class, 'getPaymentInfoForUserRt']);
        Route::get('/rt/{rtId}', [PaymentApiController::class, 'getPaymentInfoByRt']);
    });

    // Notifications API routes
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotifikasiApiController::class, 'index']);
        Route::get('/unread', [NotifikasiApiController::class, 'getUnread']);
        Route::get('/unread-count', [NotifikasiApiController::class, 'getUnreadCount']);
        Route::get('/recent', [NotifikasiApiController::class, 'getRecent']);
        Route::post('/{notification}/mark-read', [NotifikasiApiController::class, 'markAsRead']);
        Route::post('/mark-all-read', [NotifikasiApiController::class, 'markAllAsRead']);
        Route::delete('/{notification}', [NotifikasiApiController::class, 'destroy']);
        Route::delete('/all', [NotifikasiApiController::class, 'destroyAll']);
    });

    // Authentication API routes
    Route::prefix('auth')->group(function () {
        Route::post('/login', [AuthApiController::class, 'login'])->withoutMiddleware('auth:sanctum');
        Route::post('/register', [AuthApiController::class, 'register'])->withoutMiddleware('auth:sanctum');
        Route::post('/logout', [AuthApiController::class, 'logout']);
        Route::get('/user', [AuthApiController::class, 'user']);
        Route::put('/profile', [AuthApiController::class, 'updateProfile']);
        Route::put('/password', [AuthApiController::class, 'updatePassword']);
        Route::post('/forgot-password', [AuthApiController::class, 'forgotPassword'])->withoutMiddleware('auth:sanctum');
        Route::post('/reset-password', [AuthApiController::class, 'resetPassword'])->withoutMiddleware('auth:sanctum');
    });

    // Admin-only API routes
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::get('/users', [AuthApiController::class, 'getAllUsers']);
        Route::post('/users', [AuthApiController::class, 'createUser']);
        Route::put('/users/{user}', [AuthApiController::class, 'updateUser']);
        Route::delete('/users/{user}', [AuthApiController::class, 'deleteUser']);
        Route::post('/users/{user}/reset-password', [AuthApiController::class, 'resetUserPassword']);
        Route::post('/users/{user}/toggle-status', [AuthApiController::class, 'toggleUserStatus']);
        Route::get('/users/search', [AuthApiController::class, 'searchUsers']);
    });
});

// Public API routes (no authentication required)
Route::get('/health', function () {
    try {
        $checks = [
            'database' => \Illuminate\Support\Facades\DB::connection()->getPdo() ? 'ok' : 'error',
            'cache' => cache()->put('health_check', 'ok', 60) ? 'ok' : 'error',
            'storage' => is_writable(storage_path()) ? 'ok' : 'error'
        ];
        
        $allHealthy = !in_array('error', array_values($checks));
        
        return response()->json([
            'success' => true,
            'status' => $allHealthy ? 'healthy' : 'degraded',
            'checks' => $checks,
            'timestamp' => now()->toISOString()
        ], $allHealthy ? 200 : 503);
        
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'status' => 'unhealthy',
            'error' => $e->getMessage(),
            'timestamp' => now()->toISOString()
        ], 503);
    }
});
