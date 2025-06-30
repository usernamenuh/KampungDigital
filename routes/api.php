<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DashboardApiController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public API test endpoint
Route::get('/test', [DashboardApiController::class, 'test']);

// Debug route for development
Route::get('/debug', function () {
    return response()->json([
        'success' => true,
        'message' => 'API Debug endpoint working',
        'timestamp' => now()->toISOString(),
        'routes_loaded' => true,
        'database_connected' => DB::connection()->getPdo() ? true : false,
        'environment' => app()->environment(),
        'version' => app()->version()
    ]);
});

// Dashboard API routes with web authentication
Route::middleware(['auth'])->prefix('dashboard')->group(function () {
    // Core dashboard endpoints
    Route::get('/test', [DashboardApiController::class, 'test']);
    Route::get('/stats', [DashboardApiController::class, 'getStats']);
    Route::get('/monthly-data', [DashboardApiController::class, 'getMonthlyData']);
    Route::get('/activities', [DashboardApiController::class, 'getActivities']);
    Route::get('/online-status', [DashboardApiController::class, 'getOnlineStatus']);
    Route::get('/system-health', [DashboardApiController::class, 'getSystemHealth']);
    
    // Admin-only endpoints
    Route::middleware(['role:admin'])->group(function () {
        Route::post('/clear-cache', [DashboardApiController::class, 'clearCache']);
    });
});

// User authentication endpoint
Route::middleware('auth')->get('/user', function (Request $request) {
    $user = Auth::user();
    return response()->json([
        'success' => true,
        'data' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'last_activity' => $user->last_activity,
            'is_online' => $user->last_activity >= now()->subMinutes(5)
        ]
    ]);
});

// Public API endpoints for location data
Route::prefix('location')->group(function () {
    Route::get('/provinces', function () {
        try {
            return response()->json([
                'success' => true,
                'data' => DB::table('id_provinces')->get(),
                'timestamp' => now()->toISOString()
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
            $regencies = DB::table('id_regencies')
                ->where('province_code', $province_code)
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $regencies,
                'count' => $regencies->count(),
                'timestamp' => now()->toISOString()
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
            $districts = DB::table('id_districts')
                ->where('province_code', $province_code)
                ->where('regency_code', $regency_code)
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $districts,
                'count' => $districts->count(),
                'timestamp' => now()->toISOString()
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
            $villages = DB::table('id_villages')
                ->where('province_code', $province_code)
                ->where('regency_code', $regency_code)
                ->where('district_code', $district_code)
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $villages,
                'count' => $villages->count(),
                'timestamp' => now()->toISOString()
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    });
});

// Health check endpoint
Route::get('/health', function () {
    try {
        // Basic health checks
        $checks = [
            'database' => DB::connection()->getPdo() ? 'ok' : 'error',
            'cache' => cache()->put('health_check', 'ok', 60) ? 'ok' : 'error',
            'storage' => is_writable(storage_path()) ? 'ok' : 'error'
        ];
        
        $allHealthy = !in_array('error', array_values($checks));
        
        return response()->json([
            'success' => true,
            'status' => $allHealthy ? 'healthy' : 'degraded',
            'checks' => $checks,
            'timestamp' => now()->toISOString(),
            'uptime' => gmdate("H:i:s", time() - filemtime(base_path()))
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
