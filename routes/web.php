<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\BranchMessageController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\HomeSliderController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\OfferingController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicController::class, 'index'])->name('home');
Route::get('/media/slides/{slider}', [PublicController::class, 'slide'])->name('slides.show');
Route::post('/locale', [LocaleController::class, 'update'])->name('locale.update');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:login')
        ->name('login.attempt');

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])
        ->middleware('throttle:register')
        ->name('register.store');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/account/password', [AccountController::class, 'editPassword'])->name('account.password.edit');
    Route::put('/account/password', [AccountController::class, 'updatePassword'])->name('account.password.update');

    Route::get('/messages', [BranchMessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/feed', [BranchMessageController::class, 'feed'])->name('messages.feed');
    Route::get('/messages/stream', [BranchMessageController::class, 'stream'])->name('messages.stream');
    Route::get('/messages/{message}/attachment', [BranchMessageController::class, 'attachment'])->name('messages.attachment');
    Route::get('/messages/{message}/attachments/{index}', [BranchMessageController::class, 'attachmentItem'])->whereNumber('index')->name('messages.attachments.show');
    Route::post('/messages', [BranchMessageController::class, 'store'])->name('messages.store');
    Route::patch('/messages/{message}', [BranchMessageController::class, 'update'])->name('messages.update');
    Route::delete('/messages/{message}', [BranchMessageController::class, 'destroy'])->name('messages.destroy');

    Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
    Route::get('/announcements/{announcement}/image', [AnnouncementController::class, 'image'])->whereNumber('announcement')->name('announcements.image');
    Route::get('/announcements/{announcement}', [AnnouncementController::class, 'show'])->whereNumber('announcement')->name('announcements.show');
    Route::get('/announcements/{announcement}/pdf', [AnnouncementController::class, 'pdf'])->whereNumber('announcement')->name('announcements.pdf');

    Route::middleware('role:super_admin')->group(function () {
        Route::resource('branches', BranchController::class)->except(['show']);
        Route::resource('sliders', HomeSliderController::class)->except(['show', 'edit', 'update']);
        Route::resource('users', UserManagementController::class)->except(['show'])->names('admin.users');
    });

    Route::middleware('role:branch_admin|district_admin|regional_admin|super_admin')->group(function () {
        Route::resource('offerings', OfferingController::class)->except(['show']);
        Route::resource('expenses', ExpenseController::class)->except(['show']);
        Route::resource('events', EventController::class)->except(['show']);
    });

    Route::middleware('role:branch_admin|district_admin|regional_admin|super_admin')->group(function () {
        Route::resource('announcements', AnnouncementController::class)->except(['index', 'show']);
    });
});
