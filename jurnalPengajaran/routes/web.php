<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HumasMonitoringController;
use App\Http\Controllers\GuruJurnalController;
use App\Http\Controllers\GuruDashboardController;
use App\Http\Controllers\Admin\DataMasterController;

// ============ GUEST ROUTES ============
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

// ============ ROUTES UNTUK SEMUA ROLE (AUTHENTICATED) ============
Route::middleware(['auth.session'])->group(function () {
    // Root redirect ke dashboard
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });
    
    // Dashboard redirect berdasarkan role
    Route::get('/dashboard', function () {
        $role = session('user_role');
        
        if ($role === 'admin' || $role === 'humas') {
            return redirect()->route('monitoring');
        } elseif ($role === 'guru') {
            return redirect()->route('guru.dashboard');
        } elseif ($role === 'parent') {
            return redirect()->route('dashboard.timeline');
        }
        
        return redirect()->route('login');
    })->name('dashboard');
});

// ============ ADMIN & HUMAS ROUTES ============
Route::middleware(['auth.session', 'role:admin,humas'])->prefix('admin')->group(function () {
    Route::get('/monitoring', [HumasMonitoringController::class, 'index'])->name('monitoring');
    
    // Data Master
    Route::get('/data-master', [DataMasterController::class, 'index'])->name('data-master');
    Route::get('/data-master/create', [DataMasterController::class, 'create'])->name('data-master.create');
    Route::post('/data-master', [DataMasterController::class, 'store'])->name('data-master.store');
    Route::get('/data-master/{id}/edit', [DataMasterController::class, 'edit'])->name('data-master.edit');
    Route::put('/data-master/{id}', [DataMasterController::class, 'update'])->name('data-master.update');
    Route::delete('/data-master/{id}', [DataMasterController::class, 'destroy'])->name('data-master.destroy');
    
    // Generate Report
    Route::get('/report/export', [HumasMonitoringController::class, 'exportReport'])->name('report.export');
});

// ============ REMIND TEACHER ============
Route::post('/remind-teacher', [HumasMonitoringController::class, 'remindTeacher'])->name('remind-teacher');

// ============ GURU ROUTES ============
Route::middleware(['auth.session', 'role:guru'])->prefix('guru')->group(function () {
    // Halaman Pilihan Ruang Kelas & Mapel
    Route::get('/dashboard', [GuruDashboardController::class, 'index'])->name('guru.dashboard');
    
    // Halaman Input Form Jurnal Utama (membutuhkan parameter)
    Route::get('/jurnal/{kelas_id}/{mapel_id}', [GuruJurnalController::class, 'index'])->name('jurnal');
    Route::post('/jurnal', [GuruJurnalController::class, 'store'])->name('guru.jurnal.store');
});

// ============ PARENT ROUTES ============
Route::middleware(['auth.session', 'role:parent'])->prefix('parent')->group(function () {
    Route::get('/dashboard/timeline', [DashboardController::class, 'timeline'])->name('dashboard.timeline');
});

// ============ LOGOUT ============
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');