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
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\KasController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\PengaturanKasController;

// Public routes
Route::get('/', function () {
    return redirect('/login');
});

// Authentication Routes
Auth::routes();

// Protected routes
Route::middleware(['auth', 'user.status'])->group(function () {
    
    // Dashboard routes
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/masyarakat', [HomeController::class, 'masyarakatDashboard'])->name('dashboard.masyarakat');
    
    // Kas routes
    Route::prefix('kas')->name('kas.')->group(function () {
        Route::get('/', [KasController::class, 'index'])->name('index');
        Route::get('/create', [KasController::class, 'create'])->name('create')->middleware('role:admin,kades,rw,rt');
        Route::post('/', [KasController::class, 'store'])->name('store')->middleware('role:admin,kades,rw,rt');
        Route::get('/get-resident-info', [KasController::class, 'getResidentInfo'])->name('get-resident-info');
        Route::post('/generate-weekly', [KasController::class, 'generateWeekly'])->name('generate-weekly')->middleware('role:admin,kades,rw,rt');
        Route::get('/{kas}', [KasController::class, 'show'])->name('show');
        Route::get('/{kas}/edit', [KasController::class, 'edit'])->name('edit')->middleware('role:admin,kades,rw,rt');
        Route::put('/{kas}', [KasController::class, 'update'])->name('update')->middleware('role:admin,kades,rw,rt');
        Route::delete('/{kas}', [KasController::class, 'destroy'])->name('destroy')->middleware('role:admin');
        Route::post('/{kas}/bayar', [KasController::class, 'bayar'])->name('bayar');
    });
    
    // Notification routes
    Route::prefix('notifikasi')->name('notifikasi.')->group(function () {
        Route::get('/', [NotifikasiController::class, 'index'])->name('index');
        Route::post('/{notifikasi}/mark-read', [NotifikasiController::class, 'markAsRead'])->name('mark-read');
        Route::post('/mark-all-read', [NotifikasiController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::delete('/{notifikasi}', [NotifikasiController::class, 'destroy'])->name('destroy');
    });
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

    // Routes untuk Pengaturan Kas
    Route::resource('pengaturan-kas', PengaturanKasController::class);
});

// Routes khusus admin
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Resource route untuk desa - hanya admin yang bisa akses
    Route::resource('desas', DesaController::class);

    // User Management - hanya admin yang bisa akses
    Route::resource('users', UserController::class);
    Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::patch('users/{user}/change-role', [UserController::class, 'changeRole'])->name('users.change-role');
});

// Routes yang bisa diakses semua role yang sudah login
Route::middleware(['auth'])->group(function () {
    Route::get('/laporan', function () { return view('laporan.index'); })->name('laporan.index');
    Route::get('/agenda', function () { return view('agenda.index'); })->name('agenda.index');
    Route::get('/media', function () { return view('media.index'); })->name('media.index');
    Route::get('/dokumen', function () { return view('dokumen.index'); })->name('dokumen.index');
    Route::get('/pesan', function () { return view('pesan.index'); })->name('pesan.index');
});

// API Routes
Route::prefix('api')->middleware(['auth'])->group(function () {
    // Dashboard API routes
    Route::get('/dashboard/test', [DashboardApiController::class, 'test'])->name('api.dashboard.test');
    Route::get('/dashboard/stats', [DashboardApiController::class, 'getStats'])->name('api.dashboard.stats');
    Route::get('/dashboard/monthly-data', [DashboardApiController::class, 'getMonthlyData'])->name('api.dashboard.monthly');
    Route::get('/dashboard/activities', [DashboardApiController::class, 'getActivities'])->name('api.dashboard.activities');
    Route::get('/dashboard/online-status', [DashboardApiController::class, 'getOnlineStatus'])->name('api.dashboard.online-status');
    Route::get('/dashboard/system-health', [DashboardApiController::class, 'getSystemHealth'])->name('api.dashboard.system-health');
    Route::post('/dashboard/update-activity', [DashboardApiController::class, 'updateActivity'])->name('api.dashboard.update-activity');

    // Kas API routes
    Route::prefix('kas')->group(function () {
        Route::get('/stats', [\App\Http\Controllers\Api\KasApiController::class, 'getStats'])->name('api.kas.stats');
        Route::get('/', [\App\Http\Controllers\Api\KasApiController::class, 'index'])->name('api.kas.index');
        Route::post('/{kas}/pay', [\App\Http\Controllers\Api\KasApiController::class, 'pay'])->name('api.kas.pay');
        Route::get('/recent-payments', [\App\Http\Controllers\Api\KasApiController::class, 'getRecentPayments'])->name('api.kas.recent-payments');
    });

    // Notification API routes
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotifikasiController::class, 'index'])->name('api.notifications.index');
        Route::get('/recent', [NotifikasiController::class, 'getRecent'])->name('api.notifications.recent');
        Route::get('/unread-count', [NotifikasiController::class, 'getUnreadCount'])->name('api.notifications.unread-count');
        Route::post('/{notifikasi}/mark-read', [NotifikasiController::class, 'markAsRead'])->name('api.notifications.mark-read');
        Route::post('/mark-all-read', [NotifikasiController::class, 'markAllAsRead'])->name('api.notifications.mark-all-read');
        Route::delete('/{notifikasi}', [NotifikasiController::class, 'destroy'])->name('api.notifications.destroy');
        Route::delete('/', [NotifikasiController::class, 'destroyAll'])->name('api.notifications.destroy-all');
    });

    // User authentication endpoint
    Route::get('/user', function () {
        $user = Auth::user();
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'last_activity' => $user->last_activity,
                'is_online' => $user->last_activity >= now()->subMinutes(5),
                'unread_notifications' => $user->unread_notifications_count ?? 0,
            ]
        ]);
    })->name('api.user');

    // Admin only API routes
    Route::middleware(['role:admin'])->group(function () {
        Route::post('/dashboard/clear-cache', [DashboardApiController::class, 'clearCache'])->name('api.dashboard.clear-cache');
    });
});

// Public API routes
Route::prefix('api')->group(function () {
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

    Route::get('/health', function () {
        try {
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
});

// Debug routes untuk development
if (app()->environment('local')) {
    Route::get('/debug/routes', function () {
        $routes = collect(Route::getRoutes())->map(function ($route) {
            return [
                'method' => implode('|', $route->methods()),
                'uri' => $route->uri(),
                'name' => $route->getName(),
                'action' => $route->getActionName(),
            ];
        })->filter(function($route) {
            return str_contains($route['name'] ?? '', 'kas') || str_contains($route['uri'], 'kas');
        });
        
        return response()->json($routes->values()->toArray(), 200, [], JSON_PRETTY_PRINT);
    });
}

// Fallback route
Route::fallback(function () {
    return redirect('/dashboard');
});
