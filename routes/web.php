<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DesaController;

// Landing page route
Route::get('/', function () {
    return view('landing');
});

Auth::routes();

    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('dashboard');
    Route::resource('/desas', DesaController::class)->middleware(middleware: 'role:admin,rt,rw,masyarakat');

// Public API routes for Indonesian territory data
Route::prefix('api')->group(function () {
    
    // Get regencies by province code
    Route::get('/regencies/{province_code}', function ($province_code) {
        try {
            return response()->json([
                'success' => true,
                'data' => DB::table('id_regencies')
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
    
    // Get districts by province and regency code
    Route::get('/districts/{province_code}/{regency_code}', function ($province_code, $regency_code) {
        try {
            return response()->json([
                'success' => true,
                'data' => DB::table('id_districts')
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
    
    // Get villages by province, regency, and district code
    Route::get('/villages/{province_code}/{regency_code}/{district_code}', function ($province_code, $regency_code, $district_code) {
        try {
            return response()->json([
                'success' => true,
                'data' => DB::table('id_villages')
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
