<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Guards the USER area (/dashboard). Admin/staff accounts belong in the admin
 * control center, so if an admin lands here they are redirected to /admin.
 * Keeps the two surfaces strictly separate.
 */
class EnsureRegularUser
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && $request->user()->hasPermission('admin.access')) {
            return redirect()->route('admin.dashboard');
        }

        return $next($request);
    }
}
