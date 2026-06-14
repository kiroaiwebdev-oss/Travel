<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /** Allow only users holding a role with the admin.access permission. */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->hasPermission('admin.access')) {
            abort(403, 'Admin access required.');
        }

        return $next($request);
    }
}
