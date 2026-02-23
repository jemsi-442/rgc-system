<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BranchMessageController;
use App\Http\Controllers\ChurchController;
use App\Http\Controllers\DistrictController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\OfferingController;
use App\Http\Controllers\PastorController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\SlideController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserRoleController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware('throttle:api')->group(function (): void {
    Route::prefix('auth')->group(function (): void {
        Route::post('register', [AuthController::class, 'register'])->middleware('throttle:login');
        Route::post('login', [AuthController::class, 'login'])->middleware('throttle:login');

        Route::middleware('auth:sanctum')->group(function (): void {
            Route::post('logout', [AuthController::class, 'logout']);
            Route::get('me', [AuthController::class, 'profile']);
        });
    });

    Route::get('slides/public', [SlideController::class, 'publicIndex']);
    Route::get('regions/hierarchy', [RegionController::class, 'index']);
    Route::get('churches/public', [ChurchController::class, 'publicIndex']);

    Route::middleware(['auth:sanctum'])->group(function (): void {
        Route::get('profile', [ProfileController::class, 'show'])->middleware('role:super_admin,regional_admin,district_admin,branch_admin,member,admin,user');

        Route::middleware('role:super_admin')->group(function (): void {
            Route::apiResource('regions', RegionController::class);
            Route::apiResource('districts', DistrictController::class);
            Route::apiResource('users', UserController::class);
            Route::get('activity-logs', [ActivityLogController::class, 'index']);
            Route::post('users/{user}/roles/sync', [UserRoleController::class, 'sync']);
        });

        Route::middleware('role:super_admin,regional_admin,district_admin,branch_admin,admin')->group(function (): void {
            Route::apiResource('churches', ChurchController::class);
            Route::apiResource('branches', ChurchController::class)->parameters(['branches' => 'church']);
            Route::apiResource('pastors', PastorController::class);
            Route::apiResource('members', MemberController::class);
            Route::post('members/bulk', [MemberController::class, 'bulkStore']);

            Route::get('slides', [SlideController::class, 'index']);
        });

        Route::middleware('role:super_admin')->group(function (): void {
            Route::post('slides', [SlideController::class, 'store']);
            Route::put('slides/{slide}', [SlideController::class, 'update']);
            Route::delete('slides/{slide}', [SlideController::class, 'destroy']);
        });

        Route::middleware('role:super_admin,regional_admin,district_admin,branch_admin,member,admin,user')->group(function (): void {
            Route::apiResource('offerings', OfferingController::class)->only(['index', 'show']);
            Route::apiResource('expenses', ExpenseController::class)->only(['index', 'show']);
            Route::apiResource('attendance', AttendanceController::class)->only(['index', 'show']);
            Route::get('branch-chat', [BranchMessageController::class, 'index']);
            Route::post('branch-chat', [BranchMessageController::class, 'store']);
            Route::delete('branch-chat/{branchMessage}', [BranchMessageController::class, 'destroy']);
        });

        Route::middleware('role:super_admin,regional_admin,district_admin,branch_admin,admin')->group(function (): void {
            Route::apiResource('offerings', OfferingController::class)->only(['store', 'update', 'destroy']);
            Route::apiResource('expenses', ExpenseController::class)->only(['store', 'update', 'destroy']);
            Route::apiResource('attendance', AttendanceController::class)->only(['store', 'update', 'destroy']);
            Route::post('attendance/bulk', [AttendanceController::class, 'bulkStore']);

            Route::get('reports/expenses/summary', [ExpenseController::class, 'summary']);
            Route::get('reports/offerings/summary', [OfferingController::class, 'summary']);
            Route::get('reports/attendance/summary', [AttendanceController::class, 'summary']);
        });
    });
});
