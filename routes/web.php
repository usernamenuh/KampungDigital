<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DesaController;

// Landing page route
Route::get('/', function () {
    return view('landing');
});


Route::prefix('desa')->name('desa.')->group(function () {
    Route::get('/', [DesaController::class, 'index'])->name('index');
    Route::get('/create', [DesaController::class, 'create'])->name('create');
    Route::post('/', [DesaController::class, 'store'])->name('store');
    Route::get('/{desa}', [DesaController::class, 'show'])->name('show');
    Route::get('/{desa}/edit', [DesaController::class, 'edit'])->name('edit');
    Route::put('/{desa}', [DesaController::class, 'update'])->name('update');
    Route::delete('/{desa}', [DesaController::class, 'destroy'])->name('destroy');
});

// Placeholder routes for other menu items
Route::get('/penduduk', function () { return view('penduduk.index'); })->name('penduduk.index');
Route::get('/lokasi', function () { return view('lokasi.index'); })->name('lokasi.index');
Route::get('/rt-rw', function () { return view('rt-rw.index'); })->name('rt-rw.index');
Route::get('/umkm', function () { return view('umkm.index'); })->name('umkm.index');
Route::get('/wisata', function () { return view('wisata.index'); })->name('wisata.index');
Route::get('/berita', function () { return view('berita.index'); })->name('berita.index');
Route::get('/program', function () { return view('program.index'); })->name('program.index');
Route::get('/pembangunan', function () { return view('pembangunan.index'); })->name('pembangunan.index');
Route::get('/keuangan', function () { return view('keuangan.index'); })->name('keuangan.index');
Route::get('/laporan', function () { return view('laporan.index'); })->name('laporan.index');
Route::get('/agenda', function () { return view('agenda.index'); })->name('agenda.index');
Route::get('/media', function () { return view('media.index'); })->name('media.index');
Route::get('/dokumen', function () { return view('dokumen.index'); })->name('dokumen.index');
Route::get('/pesan', function () { return view('pesan.index'); })->name('pesan.index');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Dashboard route - redirect to home for now
Route::get('/dashboard', function () {
    return redirect('/home');
})->name('dashboard');

Route::resource('desas', DesaController::class);

// Public API routes for Indonesian territory data
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
