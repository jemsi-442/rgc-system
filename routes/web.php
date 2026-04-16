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
use App\Http\Controllers\OfferingPaymentController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\SystemAssistantController;
use App\Http\Controllers\SystemAssistantTopicController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicController::class, 'index'])->name('home');
Route::get('/media/slides/{slider}', [PublicController::class, 'slide'])->name('slides.show');
Route::get('/csrf-token', [AuthController::class, 'refreshCsrfToken'])->name('csrf.refresh');
Route::post('/locale', [LocaleController::class, 'update'])->name('locale.update');
Route::post('/assistant/chat', [SystemAssistantController::class, 'reply'])
    ->middleware('throttle:30,1')
    ->name('assistant.chat');
Route::post('/assistant/interactions/{interaction}/feedback', [SystemAssistantController::class, 'feedback'])
    ->middleware('throttle:30,1')
    ->name('assistant.feedback');
Route::get('/giving/{publicReference}', [OfferingPaymentController::class, 'publicShow'])
    ->middleware('throttle:payment-status')
    ->name('offerings.payments.public.show');
Route::get('/giving/{publicReference}/receipt', [OfferingPaymentController::class, 'publicReceipt'])
    ->middleware(['signed', 'throttle:payment-status'])
    ->name('offerings.payments.public.receipt');

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
    Route::get('/giving', [OfferingPaymentController::class, 'index'])->name('giving.index');
    Route::post('/giving', [OfferingPaymentController::class, 'memberStore'])->name('giving.store');

    Route::get('/account/profile', [AccountController::class, 'editProfile'])->name('account.profile.edit');
    Route::put('/account/profile', [AccountController::class, 'updateProfile'])->name('account.profile.update');
    Route::get('/account/password', [AccountController::class, 'editPassword'])->name('account.password.edit');
    Route::put('/account/password', [AccountController::class, 'updatePassword'])->name('account.password.update');

    Route::get('/messages', [BranchMessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/feed', [BranchMessageController::class, 'feed'])
        ->middleware('throttle:branch-chat-feed')
        ->name('messages.feed');
    Route::get('/messages/stream', [BranchMessageController::class, 'stream'])
        ->middleware('throttle:branch-chat-stream')
        ->name('messages.stream');
    Route::get('/messages/{message}/attachment', [BranchMessageController::class, 'attachment'])->name('messages.attachment');
    Route::get('/messages/{message}/attachments/{index}', [BranchMessageController::class, 'attachmentItem'])->whereNumber('index')->name('messages.attachments.show');
    Route::post('/messages', [BranchMessageController::class, 'store'])
        ->middleware('throttle:branch-chat-send')
        ->name('messages.store');
    Route::patch('/messages/{message}', [BranchMessageController::class, 'update'])->name('messages.update');
    Route::delete('/messages/{message}', [BranchMessageController::class, 'destroy'])->name('messages.destroy');

    Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
    Route::get('/announcements/{announcement}/image', [AnnouncementController::class, 'image'])->whereNumber('announcement')->name('announcements.image');
    Route::get('/announcements/{announcement}', [AnnouncementController::class, 'show'])->whereNumber('announcement')->name('announcements.show');
    Route::get('/announcements/{announcement}/pdf', [AnnouncementController::class, 'pdf'])->whereNumber('announcement')->name('announcements.pdf');

    Route::middleware('role:super_admin')->group(function () {
        Route::get('/branches/template/{format}', [BranchController::class, 'template'])
            ->whereIn('format', ['csv', 'xlsx'])
            ->name('branches.template');
        Route::get('/branches/template/sample/{format}', [BranchController::class, 'sampleTemplate'])
            ->whereIn('format', ['csv', 'xlsx'])
            ->name('branches.template.sample');
        Route::get('/branches/export/{format}', [BranchController::class, 'export'])
            ->whereIn('format', ['csv', 'xlsx'])
            ->name('branches.export');
        Route::get('/branches/{branch}/print', [BranchController::class, 'print'])->name('branches.print');
        Route::get('/branches/{branch}/pdf', [BranchController::class, 'pdf'])->name('branches.pdf');
        Route::get('/branches/{branch}/records/export/{format}', [BranchController::class, 'exportRecords'])
            ->whereIn('format', ['csv', 'xlsx'])
            ->name('branches.records.export');
        Route::post('/branches/import', [BranchController::class, 'import'])->name('branches.import');
        Route::post('/branches/import/confirm', [BranchController::class, 'confirmImport'])->name('branches.import.confirm');
        Route::resource('branches', BranchController::class);

        Route::patch('/sliders/{slider}/status', [HomeSliderController::class, 'updateStatus'])->name('sliders.status');
        Route::patch('/sliders/{slider}/sort-order', [HomeSliderController::class, 'updateSortOrder'])->name('sliders.sort-order');
        Route::resource('sliders', HomeSliderController::class)->except(['show']);

        Route::resource('users', UserManagementController::class)->except(['show'])->names('admin.users');
    });

    Route::middleware('role:super_admin|regional_admin')->prefix('assistant/topics')->name('assistant.topics.')->group(function () {
        Route::get('/', [SystemAssistantTopicController::class, 'index'])->name('index');
        Route::get('/export', [SystemAssistantTopicController::class, 'export'])->name('export');
        Route::get('/create', [SystemAssistantTopicController::class, 'create'])->name('create');
        Route::post('/', [SystemAssistantTopicController::class, 'store'])->name('store');
        Route::get('/{topic}/edit', [SystemAssistantTopicController::class, 'edit'])->name('edit');
        Route::put('/{topic}', [SystemAssistantTopicController::class, 'update'])->name('update');
        Route::post('/{topic}/versions/{version}/restore', [SystemAssistantTopicController::class, 'restoreVersion'])->name('versions.restore');
        Route::delete('/{topic}', [SystemAssistantTopicController::class, 'destroy'])->name('destroy');
    });

    Route::middleware('role:super_admin')->prefix('assistant/topics')->name('assistant.topics.')->group(function () {
        Route::post('/import', [SystemAssistantTopicController::class, 'import'])->name('import');
        Route::post('/restore-defaults', [SystemAssistantTopicController::class, 'restoreDefaults'])->name('restore-defaults');
    });

    Route::middleware('role:branch_admin|pastor|bishop|accountant|super_admin')->group(function () {
        Route::post('/offerings/payments', [OfferingPaymentController::class, 'store'])->name('offerings.payments.store');
        Route::resource('offerings', OfferingController::class)->except(['show']);
        Route::resource('expenses', ExpenseController::class)->except(['show']);
        Route::resource('events', EventController::class)->except(['show']);
    });

    Route::middleware('role:branch_admin|district_admin|regional_admin|pastor|bishop|accountant|super_admin')->group(function () {
        Route::post('/offerings/payments/{payment}/sync', [OfferingPaymentController::class, 'sync'])->name('offerings.payments.sync');
        Route::patch('/offerings/payments/{payment}/review', [OfferingPaymentController::class, 'review'])->name('offerings.payments.review');
        Route::patch('/offerings/payments/review-all', [OfferingPaymentController::class, 'reviewAll'])->name('offerings.payments.review-all');
    });

    Route::middleware('role:branch_admin|district_admin|regional_admin|super_admin')->group(function () {
        Route::resource('announcements', AnnouncementController::class)->except(['index', 'show']);
    });
});
