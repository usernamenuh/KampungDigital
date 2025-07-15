<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\DashboardApiController;
use App\Http\Controllers\Api\PaymentApiController;
use App\Http\Controllers\Api\KasApiController;
use App\Http\Controllers\Api\NotifikasiApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public API routes (e.g., for health check, public data)
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

// Authentication API routes
Route::post('/login', [AuthApiController::class, 'login']);

// Protected API routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthApiController::class, 'logout']);
    Route::get('/user', [AuthApiController::class, 'user']);

    // Dashboard API routes
    Route::prefix('dashboard')->group(function () {
        Route::get('/stats', [DashboardApiController::class, 'getStats']);
        Route::get('/monthly-kas', [DashboardApiController::class, 'getMonthlyKasData']);
        Route::get('/activities', [DashboardApiController::class, 'getActivities']);
        Route::get('/system-monitoring', [DashboardApiController::class, 'getSystemMonitoring']);
        Route::post('/clear-cache', [DashboardApiController::class, 'clearCache']);
        Route::get('/system-health', [DashboardApiController::class, 'getSystemHealth']);
        Route::post('/update-activity', [DashboardApiController::class, 'updateActivity']);
        Route::get('/payment-alerts', [DashboardApiController::class, 'getPaymentAlerts']); // New route for payment alerts
    });

    // Kas API routes
    Route::prefix('kas')->group(function () {
        Route::get('/stats', [KasApiController::class, 'getStats']);
        Route::get('/user/{user_id}', [KasApiController::class, 'getUserKas']);
        Route::get('/recent-payments', [KasApiController::class, 'getRecentPayments']);
    });

    // Notifications API routes
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotifikasiApiController::class, 'index']);
        Route::get('/unread', [NotifikasiApiController::class, 'getUnread']);
        Route::get('/unread-count', [NotifikasiApiController::class, 'getUnreadCount']);
        Route::get('/recent', [NotifikasiApiController::class, 'getRecent']);
        Route::post('/{notification}/read', [NotifikasiApiController::class, 'markAsRead']);
        Route::post('/mark-all-read', [NotifikasiApiController::class, 'markAllAsRead']);
        Route::delete('/{notification}', [NotifikasiApiController::class, 'destroy']);
        Route::delete('/clear-all', [NotifikasiApiController::class, 'destroyAll']);
    });

    // Payment Info API routes
    Route::prefix('payment-info')->group(function () {
        Route::get('/rt/{rt_id}', [PaymentApiController::class, 'getPaymentInfoByRt']);
        Route::get('/for-user-rt', [PaymentApiController::class, 'getPaymentInfoForUserRt']);
    });

    // Payments API routes
    Route::prefix('payment')->group(function () {
        Route::get('/index', [PaymentApiController::class, 'index']);
        Route::post('/{payment}/confirm', [PaymentApiController::class, 'confirmPayment']);
    });
});

// Public API routes for location data
Route::prefix('location')->group(function () {
    Route::get('/provinces', function () {
        try {
            return response()->json([
                'success' => true,
                'data' => \Illuminate\Support\Facades\DB::table('id_provinces')->get()
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    });

    Route::get('/regencies/{province_code}', function ($province_code) {
        try {
            return response()->json([
                'success' => true,
                'data' => \Illuminate\Support\Facades\DB::table('id_regencies')
                    ->where('province_code', $province_code)
                    ->get()
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    });

    Route::get('/districts/{province_code}/{regency_code}', function ($province_code, $regency_code) {
        try {
            return response()->json([
                'success' => true,
                'data' => \Illuminate\Support\Facades\DB::table('id_districts')
                    ->where('province_code', $province_code)
                    ->where('regency_code', $regency_code)
                    ->get()
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    });

    Route::get('/villages/{province_code}/{regency_code}/{district_code}', function ($province_code, $regency_code, $district_code) {
        try {
            return response()->json([
                'success' => true,
                'data' => \Illuminate\Support\Facades\DB::table('id_villages')
                    ->where('province_code', $province_code)
                    ->where('regency_code', $regency_code)
                    ->where('district_code', $district_code)
                    ->get()
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    });
});
