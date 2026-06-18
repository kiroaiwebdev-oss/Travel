<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AffiliateNetworkController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\CashbackController;
use App\Http\Controllers\Admin\CashbackRuleController;
use App\Http\Controllers\Admin\IntegrationController;
use App\Http\Controllers\Admin\KycController as AdminKycController;
use App\Http\Controllers\Admin\NotificationController as AdminNotificationController;
use App\Http\Controllers\Admin\OfferController;
use App\Http\Controllers\Admin\ProviderController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\SupportController as AdminSupportController;
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
    Route::get('/guide', [\App\Http\Controllers\Admin\GuideController::class, 'index'])->name('guide.index');
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
        Route::post('users/{user}/contact', [UserController::class, 'contact'])->name('users.contact');
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
        Route::delete('settings/branding', [SettingController::class, 'removeBranding'])->name('settings.branding.remove');
        // Communication channels (Email / SMS / WhatsApp) + OTP delivery
        Route::get('integrations', [IntegrationController::class, 'index'])->name('integrations.index');
        Route::put('integrations', [IntegrationController::class, 'update'])->name('integrations.update');
        Route::post('integrations/test', [IntegrationController::class, 'test'])->name('integrations.test');

        // AI travel assistant control
        Route::get('ai', [\App\Http\Controllers\Admin\AiController::class, 'index'])->name('ai.index');
        Route::put('ai', [\App\Http\Controllers\Admin\AiController::class, 'update'])->name('ai.update');
        Route::post('ai/test', [\App\Http\Controllers\Admin\AiController::class, 'test'])->name('ai.test');
    });

    // Offers / Deals catalog
    Route::middleware('permission:cms.manage')->group(function () {
        Route::resource('offers', OfferController::class)->except(['show']);
        Route::put('offers/{offer}/toggle', [OfferController::class, 'toggle'])->name('offers.toggle');
    });

    // Homepage content: trending destinations + user reviews/suggestions
    Route::middleware('permission:cms.manage')->group(function () {
        Route::get('destinations', [\App\Http\Controllers\Admin\DestinationController::class, 'index'])->name('destinations.index');
        Route::post('destinations', [\App\Http\Controllers\Admin\DestinationController::class, 'store'])->name('destinations.store');
        Route::put('destinations/{destination}', [\App\Http\Controllers\Admin\DestinationController::class, 'update'])->name('destinations.update');
        Route::put('destinations/{destination}/toggle', [\App\Http\Controllers\Admin\DestinationController::class, 'toggle'])->name('destinations.toggle');
        Route::delete('destinations/{destination}', [\App\Http\Controllers\Admin\DestinationController::class, 'destroy'])->name('destinations.destroy');

        Route::get('reviews', [\App\Http\Controllers\Admin\ReviewController::class, 'index'])->name('reviews.index');
        Route::put('reviews/{review}/approve', [\App\Http\Controllers\Admin\ReviewController::class, 'approve'])->name('reviews.approve');
        Route::put('reviews/{review}/reject', [\App\Http\Controllers\Admin\ReviewController::class, 'reject'])->name('reviews.reject');
        Route::put('reviews/{review}/feature', [\App\Http\Controllers\Admin\ReviewController::class, 'feature'])->name('reviews.feature');
        Route::delete('reviews/{review}', [\App\Http\Controllers\Admin\ReviewController::class, 'destroy'])->name('reviews.destroy');
    });

    // Affiliate networks
    Route::middleware('permission:providers.manage')->group(function () {
        Route::get('networks', [AffiliateNetworkController::class, 'index'])->name('networks.index');
        Route::post('networks', [AffiliateNetworkController::class, 'store'])->name('networks.store');
        Route::put('networks/{network}', [AffiliateNetworkController::class, 'update'])->name('networks.update');
        Route::put('networks/{network}/toggle', [AffiliateNetworkController::class, 'toggle'])->name('networks.toggle');
        Route::put('networks/{network}/secret', [AffiliateNetworkController::class, 'regenerateSecret'])->name('networks.secret');
    });

    // Support tickets
    Route::middleware('permission:support.handle')->group(function () {
        Route::get('support', [AdminSupportController::class, 'index'])->name('support.index');
        Route::get('support/{ticket}', [AdminSupportController::class, 'show'])->name('support.show');
        Route::post('support/{ticket}/reply', [AdminSupportController::class, 'reply'])->name('support.reply');
        Route::put('support/{ticket}/status', [AdminSupportController::class, 'updateStatus'])->name('support.status');

        // Contact form messages
        Route::get('contact', [\App\Http\Controllers\Admin\ContactController::class, 'index'])->name('contact.index');
        Route::get('contact/{contact}', [\App\Http\Controllers\Admin\ContactController::class, 'show'])->name('contact.show');
        Route::post('contact/{contact}/reply', [\App\Http\Controllers\Admin\ContactController::class, 'reply'])->name('contact.reply');
        Route::put('contact/{contact}/status', [\App\Http\Controllers\Admin\ContactController::class, 'updateStatus'])->name('contact.status');
    });

    // Staff & roles
    Route::middleware('permission:users.manage')->group(function () {
        Route::get('staff', [StaffController::class, 'index'])->name('staff.index');
        Route::post('staff', [StaffController::class, 'store'])->name('staff.store');
        Route::put('staff/{user}/roles', [StaffController::class, 'updateRoles'])->name('staff.roles');
    });

    // Reports / CSV exports
    Route::middleware('permission:analytics.view')->group(function () {
        Route::get('reports/users.csv', [ReportController::class, 'users'])->name('reports.users');
        Route::get('reports/cashbacks.csv', [ReportController::class, 'cashbacks'])->name('reports.cashbacks');
        Route::get('reports/withdrawals.csv', [ReportController::class, 'withdrawals'])->name('reports.withdrawals');
    });
});
