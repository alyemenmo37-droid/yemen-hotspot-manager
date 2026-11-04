<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
	Route::middleware('auth:sanctum')->group(function () {
	    Route::get('/subscriptions', [SubscriptionController::class, 'index']);
	    Route::post('/subscriptions', [SubscriptionController::class, 'store']);
	    Route::delete('/subscriptions/{id}', [SubscriptionController::class, 'destroy']);
	});
}
