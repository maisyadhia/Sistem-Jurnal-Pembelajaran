<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HumasMonitoringController;
use App\Http\Controllers\GuruJurnalController;
use App\Http\Controllers\GuruDashboardController;
use App\Http\Controllers\Admin\DataMasterController;
use App\Http\Controllers\Admin\GuruController;
use App\Http\Controllers\Admin\KelasController;
use App\Http\Controllers\Admin\MapelController;
use App\Http\Controllers\Admin\LogController;
use App\Http\Controllers\Admin\SiswaController;
use App\Http\Controllers\Admin\JadwalController;
use App\Http\Controllers\NotificationController;

// ============ GUEST ROUTES ============
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

// ============ ROUTES UNTUK SEMUA ROLE (AUTHENTICATED) ============
Route::middleware(['auth.session'])->group(function () {
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });
    
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
    
   // ====== DATA MASTER ======
Route::get('/data-master', [DataMasterController::class, 'index'])->name('data-master');
    
    // ====== GURU CRUD ======
    Route::prefix('data-master/guru')->group(function () {
        Route::get('/', [GuruController::class, 'index'])->name('data-master.guru');
        Route::get('/create', [GuruController::class, 'create'])->name('data-master.guru.create');
        Route::post('/', [GuruController::class, 'store'])->name('data-master.guru.store');
        Route::get('/{id}/edit', [GuruController::class, 'edit'])->name('data-master.guru.edit');
        Route::put('/{id}', [GuruController::class, 'update'])->name('data-master.guru.update');
        Route::delete('/{id}', [GuruController::class, 'destroy'])->name('data-master.guru.destroy');
    });
    
    // ====== KELAS CRUD ======
    Route::prefix('data-master/kelas')->group(function () {
        Route::get('/', [KelasController::class, 'index'])->name('data-master.kelas');
        Route::get('/create', [KelasController::class, 'create'])->name('data-master.kelas.create');
        Route::post('/', [KelasController::class, 'store'])->name('data-master.kelas.store');
        Route::get('/{id}/edit', [KelasController::class, 'edit'])->name('data-master.kelas.edit');
        Route::put('/{id}', [KelasController::class, 'update'])->name('data-master.kelas.update');
        Route::delete('/{id}', [KelasController::class, 'destroy'])->name('data-master.kelas.destroy');
    });
    
    // ====== MAPEL CRUD ======
    Route::prefix('data-master/mapel')->group(function () {
        Route::get('/', [MapelController::class, 'index'])->name('data-master.mapel');
        Route::get('/create', [MapelController::class, 'create'])->name('data-master.mapel.create');
        Route::post('/', [MapelController::class, 'store'])->name('data-master.mapel.store');
        Route::get('/{id}/edit', [MapelController::class, 'edit'])->name('data-master.mapel.edit');
        Route::put('/{id}', [MapelController::class, 'update'])->name('data-master.mapel.update');
        Route::delete('/{id}', [MapelController::class, 'destroy'])->name('data-master.mapel.destroy');
    });
    
    // Generate Report
    Route::get('/report/export', [HumasMonitoringController::class, 'exportReport'])->name('report.export');
});

// ====== ADMIN LOGS ======
Route::prefix('admin/logs')->group(function () {
    Route::get('/', [LogController::class, 'index'])->name('admin.logs');
    Route::get('/{id}', [LogController::class, 'show'])->name('admin.logs.show');
    Route::delete('/clear', [LogController::class, 'clear'])->name('admin.logs.clear');
});

// Di routes/web.php
Route::post('/notification/read/{id}', [NotificationController::class, 'markAsRead'])->name('notification.read');
// ============ REMIND TEACHER ============
Route::post('/remind-teacher', [HumasMonitoringController::class, 'remindTeacher'])->name('remind-teacher');


// ============ GURU ROUTES ============
Route::middleware(['auth.session', 'role:guru'])->prefix('guru')->group(function () {
    Route::get('/pilih-sesi', [GuruDashboardController::class, 'index'])->name('guru.pilih.sesi');
    Route::get('/dashboard', [GuruDashboardController::class, 'dashboard'])->name('guru.dashboard');
    Route::get('/jurnal/export-excel', [GuruDashboardController::class, 'exportExcel'])->name('guru.jurnal.export');      
    Route::get('/jurnal/{kelas_id}/{mapel_id}', [GuruJurnalController::class, 'index'])->name('guru.jurnal.form');
    Route::post('/jurnal', [GuruJurnalController::class, 'store'])->name('guru.jurnal.store');
});

// ====== SISWA CRUD ======
Route::prefix('data-master/siswa')->group(function () {
    Route::get('/', [SiswaController::class, 'index'])->name('data-master.siswa');
    Route::get('/create', [SiswaController::class, 'create'])->name('data-master.siswa.create');
    Route::post('/', [SiswaController::class, 'store'])->name('data-master.siswa.store');
    Route::get('/{id}/edit', [SiswaController::class, 'edit'])->name('data-master.siswa.edit');
    Route::put('/{id}', [SiswaController::class, 'update'])->name('data-master.siswa.update');
    Route::delete('/{id}', [SiswaController::class, 'destroy'])->name('data-master.siswa.destroy');
});

// ====== JADWAL CRUD ======
Route::prefix('data-master/jadwal')->group(function () {
    Route::get('/', [JadwalController::class, 'index'])->name('data-master.jadwal');
    Route::get('/create', [JadwalController::class, 'create'])->name('data-master.jadwal.create');
    Route::post('/', [JadwalController::class, 'store'])->name('data-master.jadwal.store');
    Route::get('/{id}/edit', [JadwalController::class, 'edit'])->name('data-master.jadwal.edit');
    Route::put('/{id}', [JadwalController::class, 'update'])->name('data-master.jadwal.update');
    Route::delete('/{id}', [JadwalController::class, 'destroy'])->name('data-master.jadwal.destroy');
});

// ============ PARENT ROUTES ============
Route::middleware(['auth.session', 'role:parent'])->prefix('parent')->group(function () {
    Route::get('/dashboard/timeline', [DashboardController::class, 'timeline'])->name('dashboard.timeline');
});

// ============ LOGOUT ============
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');