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
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public API routes
Route::prefix('v1')->group(function () {
  
  // Authentication routes
  Route::prefix('auth')->group(function () {
      Route::post('/login', [AuthApiController::class, 'login']);
      Route::post('/register', [AuthApiController::class, 'register']);
      Route::post('/forgot-password', [AuthApiController::class, 'forgotPassword']);
      Route::post('/reset-password', [AuthApiController::class, 'resetPassword']);
  });

  // Protected API routes
  Route::middleware(['auth:sanctum', 'user.status'])->group(function () {
      
      // Authentication
      Route::prefix('auth')->group(function () {
          Route::post('/logout', [AuthApiController::class, 'logout']);
          Route::get('/user', [AuthApiController::class, 'user']);
          Route::put('/profile', [AuthApiController::class, 'updateProfile']);
          Route::put('/password', [AuthApiController::class, 'updatePassword']);
      });

      // Dashboard API
      Route::prefix('dashboard')->group(function () {
          Route::get('/stats', [DashboardApiController::class, 'getStats']);
          Route::get('/recent-activities', [DashboardApiController::class, 'getActivities']);
          // Route::get('/notifications', [DashboardApiController::class, 'getNotifications']); // Uncomment if you implement this method
          // Route::get('/kas-summary', [DashboardApiController::class, 'getKasSummary']); // Uncomment if you implement this method
          // Route::get('/payment-summary', [DashboardApiController::class, 'getPaymentSummary']); // Uncomment if you implement this method
          
          // Role-specific dashboard data (using existing methods)
          Route::get('/masyarakat', [DashboardApiController::class, 'getMasyarakatStats']);
          Route::get('/rt', [DashboardApiController::class, 'getRtStats']);
          Route::get('/rw', [DashboardApiController::class, 'getRwStats']);
          Route::get('/kades', [DashboardApiController::class, 'getKadesStats']);
          Route::get('/admin', [DashboardApiController::class, 'getAdminStats']);

          // New routes for header connection status
          Route::get('/online-status', [DashboardApiController::class, 'getOnlineStatus']);
          Route::post('/update-activity', [DashboardApiController::class, 'updateUserActivity']);
          
          // Added routes for system monitoring and cache clearing
          Route::get('/system-monitoring', [DashboardApiController::class, 'getSystemMonitoring']);
          Route::post('/clear-cache', [DashboardApiController::class, 'clearCache']);
      });

      // Kas API
      Route::prefix('kas')->group(function () {
          Route::get('/', [KasApiController::class, 'index']);
          Route::get('/{kas}', [KasApiController::class, 'show']);
          Route::get('/user/{userId}', [KasApiController::class, 'getUserKas']);
          Route::get('/rt/{rtId}', [KasApiController::class, 'getRtKas']);
          Route::get('/rw/{rwId}', [KasApiController::class, 'getRwKas']);
          
          // Kas management (RT/RW/Kades/Admin only)
          Route::middleware('role:rt,rw,kades,admin')->group(function () {
              Route::post('/', [KasApiController::class, 'store']);
              Route::put('/{kas}', [KasApiController::class, 'update']);
              Route::delete('/{kas}', [KasApiController::class, 'destroy']);
              Route::post('/bulk-create', [KasApiController::class, 'bulkCreate']);
              Route::post('/bulk-update', [KasApiController::class, 'bulkUpdate']);
              Route::post('/bulk-delete', [KasApiController::class, 'bulkDelete']);
          });

          // Payment submission (Masyarakat only)
          Route::middleware('role:masyarakat')->group(function () {
              Route::post('/{kas}/submit-payment', [KasApiController::class, 'submitPayment']);
              Route::get('/{kas}/payment-info', [KasApiController::class, 'getPaymentInfo']);
          });

          // Statistics and reports
          Route::get('/stats/overview', [KasApiController::class, 'getOverviewStats']);
          Route::get('/stats/monthly', [KasApiController::class, 'getMonthlyStats']);
          Route::get('/stats/yearly', [KasApiController::class, 'getYearlyStats']);
          Route::get('/reports/export', [KasApiController::class, 'exportReport']);
      });

      // Payment API
      Route::prefix('payments')->group(function () {
          Route::get('/', [PaymentApiController::class, 'index']);
          Route::get('/{payment}', [PaymentApiController::class, 'show']);
          
          // Payment confirmation (RT/RW/Kades/Admin only)
          Route::middleware('role:rt,rw,kades,admin')->group(function () {
              Route::get('/pending', [PaymentApiController::class, 'getPendingPayments']);
              Route::post('/{payment}/confirm', [PaymentApiController::class, 'confirmPayment']);
              Route::post('/{payment}/reject', [PaymentApiController::class, 'rejectPayment']);
              Route::get('/{payment}/proof', [PaymentApiController::class, 'getProof']);
          });

          // Payment history
          Route::get('/history', [PaymentApiController::class, 'getPaymentHistory']);
          Route::get('/stats', [PaymentApiController::class, 'getPaymentStats']);
      });

      // Payment Info API
      Route::prefix('payment-info')->middleware('role:rt,rw,kades,admin')->group(function () {
          Route::get('/', [PaymentApiController::class, 'getPaymentInfos']);
          Route::post('/', [PaymentApiController::class, 'storePaymentInfo']);
          Route::get('/{paymentInfo}', [PaymentApiController::class, 'getPaymentInfo']);
          Route::put('/{paymentInfo}', [PaymentApiController::class, 'updatePaymentInfo']);
          Route::delete('/{paymentInfo}', [PaymentApiController::class, 'deletePaymentInfo']);
          Route::get('/rt/{rtId}', [PaymentApiController::class, 'getRtPaymentInfo']);
      });

      // Notifications API
      Route::prefix('notifications')->group(function () {
          Route::get('/', [NotifikasiApiController::class, 'index']);
          Route::get('/unread', [NotifikasiApiController::class, 'getUnread']);
          Route::get('/unread-count', [NotifikasiApiController::class, 'getUnreadCount']);
          Route::post('/{notification}/mark-read', [NotifikasiApiController::class, 'markAsRead']);
          Route::post('/mark-all-read', [NotifikasiApiController::class, 'markAllAsRead']);
          Route::delete('/{notification}', [NotifikasiApiController::class, 'destroy']);
          Route::delete('/delete-all', [NotifikasiApiController::class, 'destroyAll']);
          
          // Real-time notifications
          Route::get('/recent', [NotifikasiApiController::class, 'getRecent']);
          Route::post('/send', [NotifikasiApiController::class, 'sendNotification'])->middleware('role:rt,rw,kades,admin');
      });

      // File upload API
      Route::prefix('files')->group(function () {
          Route::post('/upload', [PaymentApiController::class, 'uploadFile']);
          Route::get('/{file}/download', [PaymentApiController::class, 'downloadFile']);
          Route::delete('/{file}', [PaymentApiController::class, 'deleteFile']);
      });

      // Search API
      Route::prefix('search')->group(function () {
          Route::get('/kas', [KasApiController::class, 'search']);
          Route::get('/payments', [PaymentApiController::class, 'search']);
          Route::get('/users', [AuthApiController::class, 'searchUsers'])->middleware('role:rt,rw,kades,admin');
          Route::get('/global', [DashboardApiController::class, 'globalSearch']);
      });

      // Reports API
      Route::prefix('reports')->middleware('role:rt,rw,kades,admin')->group(function () {
          Route::get('/kas', [KasApiController::class, 'getKasReport']);
          Route::get('/payments', [PaymentApiController::class, 'getPaymentReport']);
          Route::get('/financial', [DashboardApiController::class, 'getFinancialReport']);
          Route::get('/activity', [DashboardApiController::class, 'getActivityReport']);
          Route::post('/export', [DashboardApiController::class, 'exportReport']);
      });

      // Admin API
      Route::prefix('admin')->middleware('role:admin')->group(function () {
          Route::get('/users', [AuthApiController::class, 'getAllUsers']);
          Route::post('/users', [AuthApiController::class, 'createUser']);
          Route::put('/users/{user}', [AuthApiController::class, 'updateUser']);
          Route::delete('/users/{user}', [AuthApiController::class, 'deleteUser']);
          Route::post('/users/{user}/reset-password', [AuthApiController::class, 'resetUserPassword']);
          Route::post('/users/{user}/toggle-status', [AuthApiController::class, 'toggleUserStatus']);
          
          // System settings
          // Route::get('/settings', [DashboardApiController::class, 'getSystemSettings']); // Uncomment if you implement this method
          // Route::put('/settings', [DashboardApiController::class, 'updateSystemSettings']); // Uncomment if you implement this method
          
          // System stats
          // Route::get('/stats', [DashboardApiController::class, 'getSystemStats']); // Uncomment if you implement this method
          // Route::get('/logs', [DashboardApiController::class, 'getSystemLogs']); // Uncomment if you implement this method
      });
  });
});

// Health check endpoint
Route::get('/health', function () {
  return response()->json([
      'status' => 'ok',
      'timestamp' => now(),
      'version' => config('app.version', '1.0.0')
  ]);
});

// API Documentation endpoint
Route::get('/docs', function () {
  return response()->json([
      'message' => 'API Documentation',
      'version' => 'v1',
      'endpoints' => [
          'auth' => '/api/v1/auth',
          'dashboard' => '/api/v1/dashboard',
          'kas' => '/api/v1/kas',
          'payments' => '/api/v1/payments',
          'notifications' => '/api/v1/notifications',
          'reports' => '/api/v1/reports'
      ]
  ]);
});
