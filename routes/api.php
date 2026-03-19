<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\OfferingPaymentController;
use Illuminate\Support\Facades\Route;

Route::get('/regions', [LocationController::class, 'regions']);
Route::get('/districts', [LocationController::class, 'districts']);
Route::get('/branches', [LocationController::class, 'branches']);
Route::post('/payments/snippe/webhook', [OfferingPaymentController::class, 'webhook'])
    ->middleware('throttle:api')
    ->name('payments.snippe.webhook');

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:api-login');
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth.api');
});

Route::middleware(['auth.api'])->group(function () {
    Route::get('/me', [UserController::class, 'me']);
    Route::apiResource('users', UserController::class);
});
