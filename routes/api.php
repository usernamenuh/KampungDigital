<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DashboardApiController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Dashboard API Routes - TAMBAHAN BARU
Route::middleware(['auth'])->group(function () {
    // Test endpoint
    Route::get('/dashboard/test', [DashboardApiController::class, 'test']);
    
    // Main dashboard stats - INI YANG DIPERLUKAN
    Route::get('/dashboard/stats', [DashboardApiController::class, 'getStats']);
    
    // Online status
    Route::get('/dashboard/online-status', [DashboardApiController::class, 'getOnlineStatus']);
    Route::post('/dashboard/update-activity', [DashboardApiController::class, 'updateActivity']);
    
    // Activities
    Route::get('/dashboard/activities', [DashboardApiController::class, 'getActivities']);
    
    // Monthly data for charts
    Route::get('/dashboard/monthly-data', [DashboardApiController::class, 'getMonthlyData']);
    
    // System monitoring (admin only)
    Route::get('/dashboard/system-monitoring', [DashboardApiController::class, 'getSystemMonitoring']);
    Route::get('/dashboard/system-health', [DashboardApiController::class, 'getSystemHealth']);
    Route::post('/dashboard/clear-cache', [DashboardApiController::class, 'clearCache']);
});
