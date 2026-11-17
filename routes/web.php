<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MapDashboardController;

/*
|--------------------------------------------------------------------------
| ArıRota - Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [MapDashboardController::class, 'index'])->name('dashboard');
