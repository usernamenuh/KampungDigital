<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard;

Route::get('/', function () {
    return view('landing');
});

Route::get('/news', function () {
    return view('news.index');
});

Route::get('/landing', function () {
    return view('landing');
});
Auth::routes();


Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Temporary route untuk testing (hapus setelah selesai)
Route::get('/dashboard-test', function () {
    return view('dashboard');
});