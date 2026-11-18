<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MapDashboardController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HiveController;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| ArıRota - Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [MapDashboardController::class, 'index'])->name('dashboard');

Route::middleware('guest')->group(function () {
    Route::get('/giris', [AuthController::class, 'createLogin'])->name('login');
    Route::post('/giris', [AuthController::class, 'login'])->middleware('throttle:6,1');
    Route::get('/kayit', [AuthController::class, 'createRegister'])->name('register');
    Route::post('/kayit', [AuthController::class, 'register']);
});
Route::post('/cikis', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/profil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profil', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/kovanlar', [HiveController::class, 'store'])->name('hives.store');
    Route::delete('/kovanlar/{hive}', [HiveController::class, 'destroy'])->name('hives.destroy');
});

Route::prefix('yonetim')->middleware(['auth', 'admin'])->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('index');
    Route::post('/bolgeler', [AdminController::class, 'storeRegion'])->name('regions.store');
    Route::put('/bolgeler/{region}', [AdminController::class, 'updateRegion'])->name('regions.update');
    Route::post('/uyarilar', [AdminController::class, 'storeAlert'])->name('alerts.store');
    Route::get('/raporlar/bolgeler.csv', [AdminController::class, 'exportRegions'])->name('regions.export');
});
