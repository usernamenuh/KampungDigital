<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DesaController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RwController;
use App\Http\Controllers\RtController;
use App\Http\Controllers\RtRwController;
use App\Http\Controllers\Api\DashboardApiController;
use App\Http\Controllers\PendudukController;
use App\Http\Controllers\KkController;

// Landing page route
Route::get('/', function () {
    return view('landing');
});

// Routes dengan pembatasan role
Route::middleware(['auth', 'role:admin,kades,rw,rt'])->group(function () {
    // Route untuk halaman gabungan RT & RW
    Route::get('/rt-rw', [RtRwController::class, 'index'])->name('rt-rw.index');
    // Route untuk CRUD RW
    Route::resource('rw', RwController::class)->except(['index', 'show']);
    // Route untuk CRUD RT  
    Route::resource('rt', RtController::class)->except(['index', 'show']);
    // Routes untuk Penduduk - bisa diakses admin, kades, rw, rt
    Route::resource('penduduk', PendudukController::class);
    Route::get('penduduk-statistics', [PendudukController::class, 'statistics'])->name('penduduk.statistics');
    // Routes untuk Kartu Keluarga - bisa diakses admin, kades, rw, rt
    Route::resource('kk', KkController::class);
    Route::post('kk/{kk}/set-kepala-keluarga', [KkController::class, 'setKepalaKeluarga'])->name('kk.set-kepala-keluarga');
});

Route::middleware(['auth'])->group(function () {
    // Main dashboard route - akan redirect berdasarkan role
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/dashboard', [HomeController::class, 'redirectToDashboard'])->name('dashboard');
    
    // Dashboard khusus untuk setiap role
    Route::get('/dashboard/admin', function () { 
        return view('dashboards.admin'); 
    })->name('admin.dashboard')->middleware('role:admin');
    
    Route::get('/dashboard/kades', function () { 
        return view('dashboards.kades'); 
    })->name('kades.dashboard')->middleware('role:kades');
    
    Route::get('/dashboard/rw', function () { 
        return view('dashboards.rw'); 
    })->name('rw.dashboard')->middleware('role:rw');
    
    Route::get('/dashboard/rt', function () { 
        return view('dashboards.rt'); 
    })->name('rt.dashboard')->middleware('role:rt');
    
    Route::get('/dashboard/masyarakat', function () { 
        return view('dashboards.masyarakat'); 
    })->name('masyarakat.dashboard')->middleware('role:masyarakat');
});

// Resource route untuk desa - hanya admin yang bisa akses
Route::resource('desas', DesaController::class)->middleware('role:admin');


Route::middleware(['auth', 'role:admin,kades'])->group(function () {
    Route::get('/wisata', function () { return view('wisata.index'); })->name('wisata.index');
    Route::get('/berita', function () { return view('berita.index'); })->name('berita.index');
    Route::get('/program', function () { return view('program.index'); })->name('program.index');
    Route::get('/pembangunan', function () { return view('pembangunan.index'); })->name('pembangunan.index');
    Route::get('/keuangan', function () { return view('keuangan.index'); })->name('keuangan.index');
});

// Routes yang bisa diakses semua role yang sudah login
Route::middleware(['auth'])->group(function () {
    Route::get('/laporan', function () { return view('laporan.index'); })->name('laporan.index');
    Route::get('/agenda', function () { return view('agenda.index'); })->name('agenda.index');
    Route::get('/media', function () { return view('media.index'); })->name('media.index');
    Route::get('/dokumen', function () { return view('dokumen.index'); })->name('dokumen.index');
    Route::get('/pesan', function () { return view('pesan.index'); })->name('pesan.index');
});

Auth::routes();

// API Routes untuk Dashboard (Protected)
Route::prefix('api')->middleware(['auth'])->group(function () {
    // Dashboard API routes - bisa diakses semua role yang login
    Route::get('/dashboard/test', [DashboardApiController::class, 'test'])->name('api.dashboard.test');
    Route::get('/dashboard/stats', [DashboardApiController::class, 'getStats'])->name('api.dashboard.stats');
    Route::get('/dashboard/monthly-data', [DashboardApiController::class, 'getMonthlyData'])->name('api.dashboard.monthly');
    Route::get('/dashboard/activities', [DashboardApiController::class, 'getActivities'])->name('api.dashboard.activities');
    Route::get('/dashboard/online-status', [DashboardApiController::class, 'getOnlineStatus'])->name('api.dashboard.online-status');
    Route::get('/dashboard/system-health', [DashboardApiController::class, 'getSystemHealth'])->name('api.dashboard.system-health');
    
    // Admin only API routes
    Route::middleware(['role:admin'])->group(function () {
        Route::post('/dashboard/clear-cache', [DashboardApiController::class, 'clearCache'])->name('api.dashboard.clear-cache');
    });
});

// Public API routes untuk data wilayah Indonesia
Route::prefix('api')->group(function () {
    // Get provinces
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
