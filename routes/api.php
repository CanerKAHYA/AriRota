<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MapDashboardController;

/*
|--------------------------------------------------------------------------
| ArıRota - API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    // Tüm lokasyonları getir
    Route::get('/locations', [MapDashboardController::class, 'apiLocations']);

    // Belirli lokasyon detayı
    Route::get('/locations/{id}', [MapDashboardController::class, 'apiLocation']);

    // Yeni kovan konumu ekle
    Route::post('/hives', [MapDashboardController::class, 'apiAddHive']);

    // Canlı hava durumu (Open-Meteo proxy)
    Route::get('/weather', [MapDashboardController::class, 'apiWeather']);

    // Bölge ihbarı
    Route::post('/report', [MapDashboardController::class, 'apiReport']);
});
