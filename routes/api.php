<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\DashboardApiController;
use App\Http\Controllers\Api\PaymentApiController;
use App\Http\Controllers\Api\KasApiController;
use App\Http\Controllers\Api\NotifikasiApiController;
use App\Http\Controllers\PaymentInfoController;
use App\Http\Controllers\Api\DebugApiController;
use App\Models\RegProvince;
use App\Models\RegRegency;
use App\Models\RegDistrict;
use App\Models\RegVillage;
use App\Http\Controllers\SaldoController; 
use App\Http\Controllers\Api\WilayahApiController; // / Import SaldoController
use App\Http\Controllers\Api\BantuanProposalApiController; // Import BantuanProposalApiController

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
Route::middleware('auth:sanctum')->group(function () {
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
        // New route for fetching resident info
        Route::get('/get-resident-info', [KasApiController::class, 'getResidentInfo'])->name('kas.get-resident-info');
  });

  // Payment Info API routes
  // IMPORTANT: Place specific routes before apiResource to avoid conflicts
  Route::get('/payment-info/for-user-rt', [PaymentInfoController::class, 'getPaymentInfoForUserRt']);
  Route::get('/payment-info/rt/{rt_id}', [PaymentApiController::class, 'getPaymentInfoByRt']);
  
  // Use apiResource for standard CRUD operations (index, store, show, update, destroy)
  Route::apiResource('payment-info', PaymentInfoController::class);

  // Payments API routes
  Route::prefix('payment')->group(function () {
      Route::get('/index', [PaymentApiController::class, 'index']);
      Route::post('/{payment}/confirm', [PaymentApiController::class, 'confirmPayment']);
  });

  // Saldo API routes
  Route::prefix('saldo')->group(function () {
      Route::post('/transfer-kas', [SaldoController::class, 'transferKasToSaldo']);
      Route::post('/add-income', [SaldoController::class, 'addIncome']);
      Route::post('/add-expense', [SaldoController::class, 'addExpense']);
      Route::get('/history', [SaldoController::class, 'getSaldoHistory']);
  });
});
 Route::middleware(['role:admin,kades,rw'])->group(function () {
        Route::get('/bantuan-proposals', [BantuanProposalApiController::class, 'index']);
        Route::get('/bantuan-proposals/stats', [BantuanProposalApiController::class, 'getStats']);
        Route::get('/bantuan-proposals/recent', [BantuanProposalApiController::class, 'getRecentProposals']);
        Route::get('/bantuan-proposals/analytics', [BantuanProposalApiController::class, 'getAnalytics']);
        Route::get('/bantuan-proposals/{proposal}', [BantuanProposalApiController::class, 'show']);
        
        // RW can create proposals
        Route::middleware(['role:rw'])->group(function () {
            Route::post('/bantuan-proposals', [BantuanProposalApiController::class, 'store']);
        });
        
        // Kades can update proposal status
        Route::middleware(['role:kades,admin'])->group(function () {
            Route::put('/bantuan-proposals/{proposal}/status', [BantuanProposalApiController::class, 'updateStatus']);
        });
    });

// Territory API routes (public) - menggunakan reg_wilayah
Route::prefix('wilayah')->group(function () {
    // Get provinces
    Route::get('provinces', function () {
        try {
            $provinces = RegProvince::orderBy('name')->get();
            return response()->json([
                'success' => true,
                'data' => $provinces
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch provinces',
                'error' => $e->getMessage()
            ], 500);
        }
    });

    // Get regencies by province
    Route::get('regencies/{provinceId}', function ($provinceId) {
        try {
            $regencies = RegRegency::where('province_id', $provinceId)
                                  ->orderBy('name')
                                  ->get();
            return response()->json([
                'success' => true,
                'data' => $regencies
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch regencies',
                'error' => $e->getMessage()
            ], 500);
        }
    });

    // Get districts by regency
    Route::get('districts/{regencyId}', function ($regencyId) {
        try {
            $districts = RegDistrict::where('regency_id', $regencyId)
                                   ->orderBy('name')
                                   ->get();
            return response()->json([
                'success' => true,
                'data' => $districts
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch districts',
                'error' => $e->getMessage()
            ], 500);
        }
    });

    // Get villages by district
    Route::get('villages/{districtId}', function ($districtId) {
        try {
            $villages = RegVillage::where('district_id', $districtId)
                                 ->orderBy('name')
                                 ->get();
            return response()->json([
                'success' => true,
                'data' => $villages
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch villages',
                'error' => $e->getMessage()
            ], 500);
        }
    });

    // Tambahkan rute-rute baru ini untuk data wilayah
    Route::get('regencies/{province_id}', [WilayahApiController::class, 'getRegencies']);
    Route::get('districts/{province_id}/{regency_id}', [WilayahApiController::class, 'getDistricts']);
    Route::get('villages/{province_id}/{regency_id}/{district_id}', [WilayahApiController::class, 'getVillages']);
});