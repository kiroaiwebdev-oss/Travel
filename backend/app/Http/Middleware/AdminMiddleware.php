<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Gate the admin area. Unauthenticated visitors are sent to the dedicated
     * admin login (never the public user login). Authenticated non-admins are
     * denied — a regular user can never reach the admin panel.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->guest(route('admin.login'));
        }

        if ($user->status !== 'active' || ! $user->hasPermission('admin.access')) {
            abort(403, 'Administrator access required.');
        }

        return $next($request);
    }
}
