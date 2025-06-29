<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DashboardApiController;
use App\Http\Controllers\Api\DashboardController;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Debug route
Route::get('/debug', function () {
    return response()->json([
        'success' => true,
        'message' => 'API Debug endpoint working',
        'timestamp' => now(),
        'routes_loaded' => true,
        'database_connected' => DB::connection()->getPdo() ? true : false
    ]);
});

// Dashboard API routes
Route::prefix('dashboard')->group(function () {
    Route::get('/test', [DashboardApiController::class, 'test']);
    Route::get('/stats', [DashboardApiController::class, 'stats']);
    Route::get('/balance', [DashboardApiController::class, 'balance']); // Add balance endpoint
    Route::get('/gender-data', [DashboardApiController::class, 'genderData']);
    Route::get('/activities', [DashboardApiController::class, 'activities']);
    Route::get('/monthly-data', [DashboardApiController::class, 'monthlyData']);
    Route::get('/revenue-data', [DashboardApiController::class, 'revenueData']);
    Route::get('/category-data', [DashboardApiController::class, 'categoryData']);
    Route::get('/population-trend', [DashboardApiController::class, 'populationTrend']);
    Route::get('/age-distribution', [DashboardApiController::class, 'ageDistribution']);
    Route::get('/village-ranking', [DashboardApiController::class, 'villageRanking']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Test endpoint langsung
Route::get('/test', [DashboardApiController::class, 'test']);

// API untuk dropdown wilayah
Route::get('/provinces', function () {
    try {
        return response()->json([
            'success' => true,
            'data' => DB::table('id_provinces')->get()
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
            'data' => $regencies
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
            'data' => $districts
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
            'data' => $villages
        ]);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
});
