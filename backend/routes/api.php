<?php

use App\Http\Controllers\Api\AiController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\PostbackController;
use App\Http\Controllers\Api\SearchApiController;
use App\Http\Controllers\Api\WalletApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // --- Auth (JWT) ---
    Route::post('auth/register', [AuthController::class, 'register'])->middleware('throttle:10,1');
    Route::post('auth/login', [AuthController::class, 'login'])->middleware('throttle:10,1');

    // --- Public search ---
    Route::get('search', [SearchApiController::class, 'search'])->middleware('throttle:120,1');
    Route::get('categories', [SearchApiController::class, 'categories']);
    Route::get('cities', [CityController::class, 'search'])->middleware('throttle:240,1');

    // --- AI travel assistant (proxied to FastAPI sidecar) ---
    Route::post('ai/assistant', [AiController::class, 'assistant'])->middleware('throttle:30,1');

    // --- Server-to-server affiliate postback (HMAC verified, no auth) ---
    Route::post('postback/{network:slug}', [PostbackController::class, 'handle']);

    // --- Authenticated (Sanctum bearer token) ---
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('auth/me', [AuthController::class, 'me']);
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::post('auth/refresh', [AuthController::class, 'refresh']);

        Route::get('wallet', [WalletApiController::class, 'show']);
        Route::get('wallet/transactions', [WalletApiController::class, 'transactions']);
        Route::get('cashback', [WalletApiController::class, 'cashback']);
    });
});
