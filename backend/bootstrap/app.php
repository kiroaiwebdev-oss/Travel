<?php

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\AuditLogger;
use App\Http\Middleware\EnsurePermission;
use App\Http\Middleware\EnsureRegularUser;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Trust ONLY the reverse proxy (nginx/Cloudflare), not arbitrary clients, so
        // X-Forwarded-For cannot be spoofed to bypass rate limits, poison audit/IP logs,
        // or defeat referral self-referral checks. Override via TRUSTED_PROXIES (use a
        // comma-separated CIDR list, or '*' only if you fully control the network edge).
        $trustedProxies = env('TRUSTED_PROXIES', '10.0.0.0/8,172.16.0.0/12,192.168.0.0/16,127.0.0.1,::1');
        $middleware->trustProxies(
            at: $trustedProxies === '*' ? '*' : array_map('trim', explode(',', $trustedProxies)),
            headers: Request::HEADER_X_FORWARDED_FOR |
                Request::HEADER_X_FORWARDED_HOST |
                Request::HEADER_X_FORWARDED_PORT |
                Request::HEADER_X_FORWARDED_PROTO
        );

        // Route middleware aliases
        $middleware->alias([
            'admin' => AdminMiddleware::class,
            'permission' => EnsurePermission::class,
            'user.area' => EnsureRegularUser::class,
        ]);

        // Audit privileged actions globally (no-op on read-only/guest traffic)
        $middleware->append(AuditLogger::class);

        // API stateful + throttling
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);
        $middleware->throttleApi();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
