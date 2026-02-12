<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\DistrictController;
use App\Http\Controllers\ChurchController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\PastorController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\OfferingController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\UserController;

Route::prefix('v1')->group(function () {

    // ================= AUTH =================
    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);

        Route::middleware('auth:sanctum')->group(function () {
            Route::post('logout', [AuthController::class, 'logout']);
            Route::get('me', [AuthController::class, 'profile']);
            Route::post('refresh', [AuthController::class, 'refresh']);
        });
    });

    // ================= PROTECTED =================
    Route::middleware('auth:sanctum')->group(function () {

        Route::apiResource('regions', RegionController::class);
        Route::apiResource('districts', DistrictController::class);
        Route::apiResource('churches', ChurchController::class);
        Route::apiResource('members', MemberController::class);
        Route::apiResource('pastors', PastorController::class);
        Route::apiResource('expenses', ExpenseController::class);
        Route::apiResource('offerings', OfferingController::class);
        Route::apiResource('attendance', AttendanceController::class);
        Route::apiResource('users', UserController::class);

        // 🔥 BULK ROUTES
        Route::post('attendance/bulk', [AttendanceController::class, 'bulkStore']);

        // 🔥 REPORTS (we will implement later)
        Route::get('reports/expenses/summary', [ExpenseController::class, 'summary']);
        Route::get('reports/offerings/summary', [OfferingController::class, 'summary']);
        Route::get('reports/attendance/summary', [AttendanceController::class, 'summary']);
    });
});
