<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HumasMonitoringController;
use App\Http\Controllers\GuruJurnalController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Guest Routes
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

// Authenticated Routes
Route::middleware(['auth.session'])->group(function () {
    // Dashboard
    Route::get('/dashboard/timeline', [DashboardController::class, 'timeline'])->name('dashboard.timeline');
    Route::get('/dashboard', function () {
        return redirect()->route('dashboard.timeline');
    })->name('dashboard');
    
    // Humas Monitoring
    Route::get('/monitoring', [HumasMonitoringController::class, 'index'])->name('monitoring');
    
    // Guru Jurnal
    Route::get('/jurnal', [GuruJurnalController::class, 'index'])->name('jurnal');
    Route::post('/jurnal', [GuruJurnalController::class, 'store'])->name('guru.jurnal.store');
    
    // Data Master
    Route::get('/data-master', function () {
        return view('data-master.index');
    })->name('data-master');
    
    // Laporan
    Route::get('/laporan', function () {
        return view('laporan.index');
    })->name('laporan');
    
    // Logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});