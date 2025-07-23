<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DesaController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RwController;
use App\Http\Controllers\RtController;
use App\Http\Controllers\RtRwController;
use App\Http\Controllers\PendudukController;
use App\Http\Controllers\KkController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\KasController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\PengaturanKasController;
use App\Http\Controllers\PaymentInfoController;
use App\Http\Controllers\KasPaymentController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BantuanProposalController;
use App\Http\Controllers\SaldoController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Public routes
Route::get('/', function () {
  return view('landing');
});

// Authentication Routes
// Nonaktifkan rute reset password bawaan Laravel untuk mengimplementasikan OTP
Auth::routes(['verify' => false, 'reset' => false, 'register' => false]); // Menonaktifkan rute register bawaan

// Custom registration routes for NIK check and OTP
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register'); // Rute untuk menampilkan form registrasi awal
Route::post('/check-nik', [RegisterController::class, 'checkNik'])->name('check-nik'); // Rute untuk cek NIK
Route::post('/register', [RegisterController::class, 'register']); // Menggunakan metode register dari RegisterController
Route::get('/register/verify-otp', [RegisterController::class, 'showOtpForm'])->name('register.otp.form');
Route::post('/register/verify-otp', [RegisterController::class, 'verifyOtp'])->name('register.otp.verify');
Route::post('/register/resend-otp', [RegisterController::class, 'resendOtp'])->name('register.otp.resend');

// Custom routes for OTP-based password reset
Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request'); // Menampilkan form input email
Route::post('password/email', [ForgotPasswordController::class, 'sendOtp'])->name('password.email'); // Mengirim OTP
Route::get('password/verify-otp', [ResetPasswordController::class, 'showOtpVerificationForm'])->name('password.verify-otp'); // Menampilkan form verifikasi OTP
Route::post('password/reset-otp', [ResetPasswordController::class, 'resetWithOtp'])->name('password.update'); // Memproses reset password dengan OTP


// Protected routes
Route::middleware(['auth', 'user.status'])->group(function () {
  
  // Dashboard routes
  Route::get('/home', [HomeController::class, 'index'])->name('home');
  Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');
  
  // Role-specific dashboard routes
  Route::get('/dashboard/masyarakat', [HomeController::class, 'masyarakatDashboard'])
      ->name('dashboard.masyarakat')
      ->middleware('role:masyarakat');
  
  Route::get('/dashboard/rt', [HomeController::class, 'rtDashboard'])
      ->name('dashboard.rt')
      ->middleware('role:rt');
  
  Route::get('/dashboard/rw', [HomeController::class, 'rwDashboard'])
      ->name('dashboard.rw')
      ->middleware('role:rw');
  
  Route::get('/dashboard/kades', [HomeController::class, 'kadesDashboard'])
      ->name('dashboard.kades')
      ->middleware('role:kades');
  
  Route::get('/dashboard/admin', [HomeController::class, 'adminDashboard'])
      ->name('dashboard.admin')
      ->middleware('role:admin');

  // Kas Management Routes - BROKEN DOWN FROM RESOURCE
  Route::prefix('kas')->name('kas.')->group(function () {
      // Basic CRUD routes
      Route::get('/', [KasController::class, 'index'])->name('index');
      Route::get('/create', [KasController::class, 'create'])->name('create')->middleware('role:rt,rw,kades,admin');
      Route::post('/', [KasController::class, 'store'])->name('store')->middleware('role:rt,rw,kades,admin');
      Route::get('/{kas}', [KasController::class, 'show'])->name('show');
      Route::get('/{kas}/edit', [KasController::class, 'edit'])->name('edit')->middleware('role:rt,rw,kades,admin');
      Route::put('/{kas}', [KasController::class, 'update'])->name('update')->middleware('role:rt,rw,kades,admin');
      Route::delete('/{kas}', [KasController::class, 'destroy'])->name('destroy')->middleware('role:admin');
      
      // AJAX routes - put these BEFORE the resource routes to avoid conflicts
      Route::get('/ajax/get-resident-info', [KasController::class, 'getResidentInfo'])->name('ajax.get-resident-info')->middleware('role:rt,rw,kades,admin');
      
      // Konfirmasi ulang route - accessible by both masyarakat and officials
      Route::post('/{kas}/konfirmasi-ulang', [KasController::class, 'konfirmasiUlang'])->name('konfirmasi-ulang');
      
      // Masyarakat specific routes
      Route::middleware('role:masyarakat')->group(function () {
          Route::get('/{kas}/payment-form', [KasPaymentController::class, 'showPaymentForm'])->name('payment.form');
          Route::post('/{kas}/submit-payment', [KasPaymentController::class, 'submitPayment'])->name('payment.submit');
          Route::post('/{kas}/payment-process', [KasPaymentController::class, 'processPayment'])->name('payment.process');
          Route::get('/{kas}/payment-success', [KasPaymentController::class, 'paymentSuccess'])->name('payment.success');
      });
      
      // RT/RW/Kades/Admin specific routes (custom routes not covered by resource)
      Route::middleware('role:rt,rw,kades,admin')->group(function () {
          Route::post('/generate-weekly', [KasController::class, 'generateWeekly'])->name('generate-weekly');
          Route::post('/{kas}/bayar', [KasController::class, 'bayar'])->name('bayar');
          Route::post('/{kas}/tolak', [KasController::class, 'tolak'])->name('tolak');
          Route::post('/bulk-create', [KasController::class, 'bulkCreate'])->name('bulk.create');
          Route::post('/bulk-update', [KasController::class, 'bulkUpdate'])->name('bulk.update');
          Route::post('/bulk-delete', [KasController::class, 'bulkDelete'])->name('bulk.delete');
      });

      // Payment Management Routes - ALL ROLES CAN ACCESS PAYMENTS LIST
      Route::middleware('role:rt,rw,kades,admin')->group(function () {
          Route::get('/payments-list', [KasPaymentController::class, 'paymentsList'])->name('payments.list');
          Route::get('/{kas}/payment-proof', [KasPaymentController::class, 'showProof'])->name('payments.proof');
          Route::get('/{kas}/download-proof', [KasPaymentController::class, 'downloadProof'])->name('payments.download.proof');
      });

      // Payment confirmation - only for RT/RW/Kades/Admin
      Route::middleware('role:rt,rw,kades,admin')->group(function () {
          Route::post('/{kas}/confirm-payment', [KasPaymentController::class, 'confirmPayment'])->name('payments.confirm');
      });
  });

  // Payment Management Routes
  Route::prefix('payments')->name('payments.')->middleware('role:rt,rw,kades,admin')->group(function () {
      Route::get('/list', [KasPaymentController::class, 'paymentsList'])->name('list');
      Route::post('/{kas}/confirm', [KasPaymentController::class, 'confirmPayment'])->name('confirm'); 
      Route::get('/{kas}/proof', [KasPaymentController::class, 'showProof'])->name('proof');
      Route::get('/{kas}/download-proof', [KasPaymentController::class, 'downloadProof'])->name('download.proof');
  });

  Route::get('/payments/{kas}/success', [KasPaymentController::class, 'paymentSuccess'])
      ->name('payments.success')
      ->middleware('role:masyarakat');

  // Payment Info Management Routes
  Route::resource('payment-info', PaymentInfoController::class)->middleware('role:rt,rw,kades,admin');
  Route::post('payment-info/{payment_info}/toggle-status', [PaymentInfoController::class, 'toggleStatus'])
      ->name('payment-info.toggle-status')
      ->middleware('role:rt,rw,kades,admin');

  // Notification Routes
  Route::prefix('notifikasi')->name('notifikasi.')->group(function () {
      Route::get('/', [NotifikasiController::class, 'index'])->name('index');
      Route::post('/{notifikasi}/mark-read', [NotifikasiController::class, 'markAsRead'])->name('mark.read');
      Route::post('/mark-all-read', [NotifikasiController::class, 'markAllAsRead'])->name('mark.all.read');
      Route::delete('/{notifikasi}', [NotifikasiController::class, 'destroy'])->name('destroy');
      Route::delete('/delete-all', [NotifikasiController::class, 'destroyAll'])->name('destroy.all');
      
      Route::get('/recent', [NotifikasiController::class, 'getRecent'])->name('recent');
      Route::get('/unread-count', [NotifikasiController::class, 'getUnreadCount'])->name('unread.count');
  });

  // Profile and Settings Routes
  Route::prefix('profile')->name('profile.')->group(function () {
      Route::get('/', [ProfileController::class, 'index'])->name('index');
      Route::put('/', [ProfileController::class, 'update'])->name('update');
      Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
  });

  // Admin specific routes
  Route::middleware('role:admin')->group(function () {
      Route::resource('desas', DesaController::class);
      Route::resource('users', UserController::class);
      Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
      Route::patch('users/{user}/change-role', [UserController::class, 'change-role'])->name('users.change-role');

      Route::prefix('admin')->name('admin.')->group(function () {
          Route::get('/users', [HomeController::class, 'users'])->name('users');
          Route::get('/users/{user}/edit', [HomeController::class, 'editUser'])->name('users.edit');
          Route::put('/users/{user}', [HomeController::class, 'updateUser'])->name('users.update');
          Route::delete('/users/{user}', [UserController::class, 'deleteUser'])->name('users.delete');
          
          Route::get('/settings', [HomeController::class, 'settings'])->name('settings');
          Route::put('/settings', [HomeController::class, 'updateSettings'])->name('settings.update');
          
          Route::get('/reports', [HomeController::class, 'reports'])->name('reports');
          Route::get('/reports/kas', [HomeController::class, 'kasReports'])->name('reports.kas');
          Route::get('/reports/payments', [HomeController::class, 'paymentReports'])->name('reports.payments');
          Route::get('/reports/export', [HomeController::class, 'exportReports'])->name('reports.export');
          
          // Admin proposal routes - ADDED THIS LINE
          Route::get('/proposals', [BantuanProposalController::class, 'adminIndex'])->name('proposals.index');
      });
  });

  // Existing routes with role-based middleware
  Route::middleware(['role:admin,kades,rw,rt'])->group(function () {
      Route::get('/rt-rw', [RtRwController::class, 'index'])->name('rt-rw.index');
      Route::resource('rw', RwController::class)->except(['index', 'show']);
      Route::resource('rt', RtController::class)->except(['index', 'show']);
      Route::resource('penduduk', PendudukController::class);
      Route::get('penduduk-statistics', [PendudukController::class, 'statistics'])->name('penduduk.statistics');
      Route::resource('kk', KkController::class);
      Route::post('kk/{kk}/set-kepala-keluarga', [KkController::class, 'setKepalaKeluarga'])->name('kk.set-kepala-keluarga');
      Route::resource('pengaturan-kas', PengaturanKasController::class);
  });

  // Bantuan Proposal Routes - FIXED ROUTES
  Route::middleware(['role:admin,kades,rw'])->group(function () {
      // RW routes - for creating and viewing own proposals
      Route::middleware(['role:rw'])->group(function () {
          Route::get('/bantuan-proposals', [BantuanProposalController::class, 'indexRw'])->name('bantuan-proposals.index');
          Route::get('/bantuan-proposals/create', [BantuanProposalController::class, 'create'])->name('bantuan-proposals.create');
          Route::post('/bantuan-proposals', [BantuanProposalController::class, 'store'])->name('bantuan-proposals.store');
      });
      
      // Kades routes - for reviewing and processing proposals
      Route::middleware(['role:kades,admin'])->group(function () {
          Route::get('/bantuan-proposals/kades', [BantuanProposalController::class, 'indexKades'])->name('bantuan-proposals.kades.index');
           Route::get('/bantuan-proposals/admin', [BantuanProposalController::class, 'adminIndex'])->name('bantuan-proposals.admin.index');
          Route::get('/bantuan-proposals/{proposal}/process', [BantuanProposalController::class, 'process'])->name('bantuan-proposals.process');
          Route::put('/bantuan-proposals/{proposal}/status', [BantuanProposalController::class, 'updateStatus'])->name('bantuan-proposals.update-status');
      });
      
      // Common routes for all roles
      Route::get('/bantuan-proposals/{proposal}', [BantuanProposalController::class, 'show'])->name('bantuan-proposals.show');
      Route::get('/bantuan-proposals/{proposal}/download', [BantuanProposalController::class, 'downloadFile'])->name('bantuan-proposals.download');
  });

  // Saldo Management Routes
  Route::prefix('saldo')->name('saldo.')->middleware('role:rt,rw,kades,admin')->group(function () {
      Route::get('/', [SaldoController::class, 'index'])->name('index');
      Route::post('/transfer-kas', [SaldoController::class, 'transferKas'])->name('transfer-kas');
      Route::post('/add-income', [SaldoController::class, 'addIncome'])->name('add-income');
      Route::post('/add-expense', [SaldoController::class, 'addExpense'])->name('add-expense');
      Route::get('/transactions', [SaldoController::class, 'transactions'])->name('transactions');
      Route::get('/history', [SaldoController::class, 'history'])->name('history');
  });
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


// Debug routes for development
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
          return str_contains($route['name'] ?? '', 'kas') || str_contains($route['uri'], 'kas') || str_contains($route['name'] ?? '', 'payment');
      });
      
      return response()->json($routes->values()->toArray(), 200, [], JSON_PRETTY_PRINT);
  });
}

// Logout Route (to handle POST request from logout button)
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
