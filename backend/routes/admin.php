<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\CashbackRuleController;
use App\Http\Controllers\Admin\ProviderController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WithdrawalController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/analytics', [AdminDashboardController::class, 'analytics'])->name('analytics');

    // Providers + their encrypted API keys (becomes active instantly on save).
    Route::middleware('permission:providers.manage')->group(function () {
        Route::resource('providers', ProviderController::class)->except(['show']);
        Route::put('providers/{provider}/toggle', [ProviderController::class, 'toggle'])->name('providers.toggle');
        Route::put('providers/{provider}/config', [ProviderController::class, 'updateConfig'])->name('providers.config');
    });

    Route::middleware('permission:cashback.manage')->group(function () {
        Route::resource('cashback-rules', CashbackRuleController::class)->except(['show']);
    });

    Route::middleware('permission:users.view')->group(function () {
        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
    });
    Route::middleware('permission:users.manage')->group(function () {
        Route::put('users/{user}/status', [UserController::class, 'updateStatus'])->name('users.status');
    });

    Route::middleware('permission:withdrawals.approve')->group(function () {
        Route::get('withdrawals', [WithdrawalController::class, 'index'])->name('withdrawals.index');
        Route::put('withdrawals/{withdrawal}/approve', [WithdrawalController::class, 'approve'])->name('withdrawals.approve');
        Route::put('withdrawals/{withdrawal}/reject', [WithdrawalController::class, 'reject'])->name('withdrawals.reject');
    });

    Route::middleware('permission:settings.manage')->group(function () {
        Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
        Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
    });
});
