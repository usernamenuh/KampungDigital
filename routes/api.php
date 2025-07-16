<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\DashboardApiController;
use App\Http\Controllers\Api\PaymentApiController;
use App\Http\Controllers\Api\KasApiController;
use App\Http\Controllers\Api\NotifikasiApiController;
use App\Http\Controllers\PaymentInfoController; // Import PaymentInfoController

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public API routes
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
Route::post('/auth/login', [AuthApiController::class, 'login']);
Route::post('/auth/register', [AuthApiController::class, 'register']);

// Protected API routes (require authentication)
Route::middleware('auth:web')->group(function () {
  // Auth routes
  Route::post('/auth/logout', [AuthApiController::class, 'logout']);
  Route::get('/auth/user', [AuthApiController::class, 'user']);
  Route::put('/auth/profile', [AuthApiController::class, 'updateProfile']);
  Route::put('/auth/password', [AuthApiController::class, 'updatePassword']);
  
  // User management routes (Admin only)
  Route::prefix('users')->group(function () {
      Route::get('/search', [AuthApiController::class, 'searchUsers']);
      Route::get('/', [AuthApiController::class, 'getAllUsers']);
      Route::post('/', [AuthApiController::class, 'createUser']);
      Route::put('/{targetUser}/toggle-status', [AuthApiController::class, 'toggleUserStatus']);
  });

  // Dashboard API routes
  Route::prefix('dashboard')->group(function () {
      Route::get('/stats', [DashboardApiController::class, 'getStats']);
      Route::get('/monthly-kas', [DashboardApiController::class, 'getMonthlyKasData']);
      Route::get('/activities', [DashboardApiController::class, 'getActivities']);
      Route::get('/system-monitoring', [DashboardApiController::class, 'getSystemMonitoring']);
      Route::post('/clear-cache', [DashboardApiController::class, 'clearCache']);
      Route::get('/system-health', [DashboardApiController::class, 'getSystemHealth']);
      Route::post('/update-activity', [DashboardApiController::class, 'updateActivity']);
      Route::get('/payment-alerts', [DashboardApiController::class, 'getPaymentAlerts']);
      Route::get('/aggregated-payment-info-rw', [DashboardApiController::class, 'getAggregatedPaymentInfoForRw']);
      // New routes for dashboard summary and recent payments
      Route::get('/monthly-summary', [DashboardApiController::class, 'getMonthlySummary']);
      Route::get('/recent-payments', [DashboardApiController::class, 'getRecentPayments']);
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

  // Payment Info API routes (using PaymentInfoController directly for CRUD)
  Route::prefix('payment-info')->group(function () {
      Route::get('/', [PaymentInfoController::class, 'index']); // For listing/fetching current RT's info
      Route::post('/', [PaymentInfoController::class, 'store']);
      Route::post('/{paymentInfo}', [PaymentInfoController::class, 'update']); // Use POST for PUT/PATCH
      Route::delete('/{paymentInfo}', [PaymentInfoController::class, 'destroy']);
      Route::get('/rt/{rt_id}', [PaymentApiController::class, 'getPaymentInfoByRt']); // Keep this for specific RT lookup if needed elsewhere
      Route::get('/for-user-rt', [PaymentApiController::class, 'getPaymentInfoForUserRt']); // Keep this for specific user's RT lookup if needed elsewhere
  });

  // Payments API routes
  Route::prefix('payment')->group(function () {
      Route::get('/index', [PaymentApiController::class, 'index']);
      Route::post('/{payment}/confirm', [PaymentApiController::class, 'confirmPayment']);
  });
});
