<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Force HTTPS links in production (TLS terminated at Cloudflare/Nginx).
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Named rate limiters. `api` is applied to all API routes via throttleApi()
        // in bootstrap/app.php — without this definition every API request throws
        // "Rate limiter [api] is not defined".
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(120)->by($request->user()?->id ?: $request->ip());
        });
    }
}
