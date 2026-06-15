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

// Dynamic PWA manifest (reflects admin-uploaded branding/icon).
Route::get('/app.webmanifest', \App\Http\Controllers\ManifestController::class)->name('pwa.manifest');

// AI travel assistant (chat UI)
Route::get('/assistant', [\App\Http\Controllers\AssistantController::class, 'show'])->name('assistant');

// Trending destinations (full premium grid — used by mobile "See all")
Route::get('/destinations', [\App\Http\Controllers\DestinationController::class, 'index'])->name('destinations');

// Static / legal pages
Route::get('/about', [\App\Http\Controllers\PageController::class, 'about'])->name('about');
Route::get('/privacy', [\App\Http\Controllers\PageController::class, 'privacy'])->name('privacy');
Route::get('/refund', [\App\Http\Controllers\PageController::class, 'refund'])->name('refund');
Route::get('/terms', [\App\Http\Controllers\PageController::class, 'terms'])->name('terms');
Route::get('/contact', [\App\Http\Controllers\ContactController::class, 'show'])->name('contact');
Route::post('/contact', [\App\Http\Controllers\ContactController::class, 'store'])->middleware('throttle:5,1')->name('contact.store');

// Public reviews & suggestions (moderated before display)
Route::post('/reviews', [\App\Http\Controllers\ReviewController::class, 'store'])->middleware('throttle:5,1')->name('reviews.store');

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

// --- Authenticated user dashboard (regular users only — admins redirected to /admin) ---
Route::middleware(['auth', 'user.area'])->prefix('dashboard')->name('dashboard.')->group(function () {
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
    Route::post('/profile/avatar', [DashboardController::class, 'updateAvatar'])->middleware('throttle:10,1')->name('profile.avatar');

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
