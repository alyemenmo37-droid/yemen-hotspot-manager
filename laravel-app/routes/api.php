<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CardController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\MikrotikApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
/*
|--------------------------------------------------------------------------
| API Routes (for frontend polling)
|--------------------------------------------------------------------------
*/

Route::middleware('web')->group(function () {
    Route::prefix('mikrotik')->name('api.mikrotik.')->group(function () {
        Route::get('/status', [MikrotikApiController::class, 'status'])->name('status');
        Route::get('/stats', [MikrotikApiController::class, 'stats'])->name('stats');
        Route::get('/logs', [MikrotikApiController::class, 'recentLogs'])->name('logs');
    });
});
/*
|--------------------------------------------------------------------------
| API Routes (v1)
|--------------------------------------------------------------------------
*/

// ═══════════════════════════════════════
// Public Routes (بدون توكن)
// ═══════════════════════════════════════
Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:5,1');  // 5 محاولات/دقيقة

// ═══════════════════════════════════════
// Protected Routes (تحتاج توكن)
// ═══════════════════════════════════════
Route::middleware('auth:sanctum')->group(function () {
    
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/dashboard/mikrotik', [DashboardController::class, 'mikrotik']);
    
    // Cards
    Route::get('/cards/stats', [CardController::class, 'stats']);
    Route::get('/cards', [CardController::class, 'index']);
    Route::get('/cards/{card}', [CardController::class, 'show']);
    Route::post('/cards', [CardController::class, 'store']);
    Route::post('/cards/{card}/activate', [CardController::class, 'activate']);
    Route::delete('/cards/{card}', [CardController::class, 'destroy']);
});
