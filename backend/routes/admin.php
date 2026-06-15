<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\CashbackController;
use App\Http\Controllers\Admin\CashbackRuleController;
use App\Http\Controllers\Admin\KycController as AdminKycController;
use App\Http\Controllers\Admin\NotificationController as AdminNotificationController;
use App\Http\Controllers\Admin\ProviderController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WithdrawalController;
use Illuminate\Support\Facades\Route;

// --- Dedicated admin authentication (separate from user login) ---
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [AdminAuthController::class, 'show'])->name('login');
    Route::post('login', [AdminAuthController::class, 'login'])->middleware('throttle:6,1')->name('login.attempt');
    Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');
});

// --- Admin control center (admin middleware = auth + active + admin.access) ---
Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/analytics', [AdminDashboardController::class, 'analytics'])->middleware('permission:analytics.view')->name('analytics');

    // Providers + encrypted API keys (active instantly on save)
    Route::middleware('permission:providers.manage')->group(function () {
        Route::resource('providers', ProviderController::class)->except(['show']);
        Route::put('providers/{provider}/toggle', [ProviderController::class, 'toggle'])->name('providers.toggle');
        Route::put('providers/{provider}/config', [ProviderController::class, 'updateConfig'])->name('providers.config');
    });

    // Cashback rules
    Route::middleware('permission:cashback.manage')->group(function () {
        Route::resource('cashback-rules', CashbackRuleController::class)->except(['show']);
        // Cashback transactions ledger control
        Route::get('cashbacks', [CashbackController::class, 'index'])->name('cashbacks.index');
        Route::put('cashbacks/{cashback}/confirm', [CashbackController::class, 'confirm'])->name('cashbacks.confirm');
        Route::put('cashbacks/{cashback}/mature', [CashbackController::class, 'mature'])->name('cashbacks.mature');
        Route::put('cashbacks/{cashback}/reject', [CashbackController::class, 'reject'])->name('cashbacks.reject');
    });

    // Bookings monitor
    Route::middleware('permission:analytics.view')->group(function () {
        Route::get('bookings', [BookingController::class, 'index'])->name('bookings.index');
        Route::put('bookings/{booking}/status', [BookingController::class, 'updateStatus'])->name('bookings.status');
    });

    // Users
    Route::middleware('permission:users.view')->group(function () {
        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
    });
    Route::middleware('permission:users.manage')->group(function () {
        Route::put('users/{user}/status', [UserController::class, 'updateStatus'])->name('users.status');
        Route::put('users/{user}/adjust', [UserController::class, 'adjustWallet'])->name('users.adjust');
    });

    // Withdrawals
    Route::middleware('permission:withdrawals.approve')->group(function () {
        Route::get('withdrawals', [WithdrawalController::class, 'index'])->name('withdrawals.index');
        Route::put('withdrawals/{withdrawal}/process', [WithdrawalController::class, 'process'])->name('withdrawals.process');
        Route::put('withdrawals/{withdrawal}/approve', [WithdrawalController::class, 'approve'])->name('withdrawals.approve');
        Route::put('withdrawals/{withdrawal}/reject', [WithdrawalController::class, 'reject'])->name('withdrawals.reject');
    });

    // KYC review
    Route::middleware('permission:users.manage')->group(function () {
        Route::get('kyc', [AdminKycController::class, 'index'])->name('kyc.index');
        Route::put('kyc/{user}/approve', [AdminKycController::class, 'approve'])->name('kyc.approve');
        Route::put('kyc/{user}/reject', [AdminKycController::class, 'reject'])->name('kyc.reject');
    });

    // Admin push / broadcast notifications
    Route::middleware('permission:cms.manage')->group(function () {
        Route::get('notifications', [AdminNotificationController::class, 'index'])->name('notifications.index');
        Route::post('notifications', [AdminNotificationController::class, 'send'])->name('notifications.send');
    });

    // Audit logs
    Route::middleware('permission:audit.view')->group(function () {
        Route::get('audit', [AuditLogController::class, 'index'])->name('audit.index');
    });

    // Settings
    Route::middleware('permission:settings.manage')->group(function () {
        Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
        Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
    });
});
