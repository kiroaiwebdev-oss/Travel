<?php

use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\OtpController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RedirectController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\KycController;
use App\Http\Controllers\User\SavedItemController;
use App\Http\Controllers\User\SupportController;
use App\Http\Controllers\User\WalletController;
use App\Http\Controllers\User\WithdrawalController;
use Illuminate\Support\Facades\Route;

// --- Public ---
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/search', [SearchController::class, 'index'])->name('search');

// Affiliate click-out -> provider deep-link (signed).
Route::get('/go/{provider:slug}', [RedirectController::class, 'out'])
    ->middleware('signed')
    ->name('go');

// --- Guest auth ---
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:6,1');

    // Passwordless email OTP login
    Route::get('/login/otp', [OtpController::class, 'showRequest'])->name('login.otp');
    Route::post('/login/otp', [OtpController::class, 'send'])->middleware('throttle:6,1')->name('login.otp.send');
    Route::get('/login/otp/verify', [OtpController::class, 'showVerify'])->name('login.otp.verify.show');
    Route::post('/login/otp/verify', [OtpController::class, 'verify'])->middleware('throttle:10,1')->name('login.otp.verify');

    Route::get('/register', [RegisterController::class, 'show'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->middleware('throttle:6,1');

    Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('auth.google');
    Route::get('/auth/google/callback', [GoogleController::class, 'callback']);
});

Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

// --- Authenticated user dashboard ---
Route::middleware('auth')->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('index');
    Route::get('/wallet', [WalletController::class, 'index'])->name('wallet');
    Route::get('/cashback', [WalletController::class, 'cashback'])->name('cashback');
    Route::get('/bookings', [DashboardController::class, 'bookings'])->name('bookings');
    Route::get('/saved', [SavedItemController::class, 'index'])->name('saved');
    Route::post('/saved', [SavedItemController::class, 'store'])->name('saved.store');
    Route::delete('/saved/{savedItem}', [SavedItemController::class, 'destroy'])->name('saved.destroy');
    Route::get('/referrals', [DashboardController::class, 'referrals'])->name('referrals');
    Route::get('/notifications', [DashboardController::class, 'notifications'])->name('notifications');
    Route::get('/profile', [DashboardController::class, 'profile'])->name('profile');
    Route::put('/profile', [DashboardController::class, 'updateProfile'])->name('profile.update');

    Route::get('/withdrawals', [WithdrawalController::class, 'index'])->name('withdrawals');
    Route::post('/withdrawals', [WithdrawalController::class, 'store'])->middleware('throttle:4,1')->name('withdrawals.store');

    Route::get('/kyc', [KycController::class, 'show'])->name('kyc');
    Route::post('/kyc', [KycController::class, 'submit'])->middleware('throttle:6,1')->name('kyc.submit');

    Route::get('/support', [SupportController::class, 'index'])->name('support');
    Route::post('/support', [SupportController::class, 'store'])->name('support.store');
    Route::get('/support/{ticket}', [SupportController::class, 'show'])->name('support.show');
    Route::post('/support/{ticket}/reply', [SupportController::class, 'reply'])->name('support.reply');
});

// --- Admin control center ---
require __DIR__.'/admin.php';
