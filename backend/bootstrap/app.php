<?php

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\AuditLogger;
use App\Http\Middleware\EnsurePermission;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Route middleware aliases
        $middleware->alias([
            'admin' => AdminMiddleware::class,
            'permission' => EnsurePermission::class,
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
