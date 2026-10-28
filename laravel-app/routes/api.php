<?php

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
