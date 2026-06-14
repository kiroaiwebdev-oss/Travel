<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Records privileged, state-changing admin actions to the audit trail (OWASP A09).
 * Only logs authenticated, non-GET requests under /admin to avoid noise.
 */
class AuditLogger
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        try {
            $user = $request->user();
            if ($user
                && ! in_array($request->method(), ['GET', 'HEAD', 'OPTIONS'], true)
                && $request->is('admin*')
                && $response->getStatusCode() < 400
            ) {
                AuditLog::create([
                    'user_id' => $user->id,
                    'action' => $request->method().' '.$request->path(),
                    'new_values' => $this->safePayload($request),
                    'ip_address' => $request->ip(),
                    'user_agent' => (string) $request->userAgent(),
                    'created_at' => now(),
                ]);
            }
        } catch (\Throwable) {
            // Auditing must never break the response.
        }

        return $response;
    }

    private function safePayload(Request $request): array
    {
        return collect($request->except([
            'password', 'password_confirmation', 'api_key', 'secret_key',
            '_token', 'config', 'payout_details',
        ]))->take(40)->all();
    }
}
