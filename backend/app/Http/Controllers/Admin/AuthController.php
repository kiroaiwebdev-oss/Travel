<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/**
 * Dedicated, hardened admin authentication — completely separate from the public
 * user login. Only accounts holding the `admin.access` permission may sign in here;
 * a regular user with valid credentials is rejected.
 */
class AuthController extends Controller
{
    public function show(): View|RedirectResponse
    {
        if (Auth::check() && Auth::user()->hasPermission('admin.access')) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Authenticate against the web guard.
        if (! Auth::attempt($data, $request->boolean('remember'))) {
            $this->audit($request, 'admin.login.failed', $data['email']);
            throw ValidationException::withMessages(['email' => 'These credentials do not match our records.']);
        }

        $user = Auth::user();

        // Hard gate: only admins may use this entry point.
        if ($user->status !== 'active' || ! $user->hasPermission('admin.access')) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            $this->audit($request, 'admin.login.denied', $data['email']);
            throw ValidationException::withMessages(['email' => 'This account is not authorized for the admin panel.']);
        }

        $request->session()->regenerate();
        $user->forceFill(['last_login_at' => now(), 'last_login_ip' => $request->ip()])->save();
        $this->audit($request, 'admin.login.success', $user->email);

        return redirect()->intended(route('admin.dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        $this->audit($request, 'admin.logout', Auth::user()?->email);
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    private function audit(Request $request, string $action, ?string $email): void
    {
        try {
            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => $action,
                'new_values' => ['email' => $email],
                'ip_address' => $request->ip(),
                'user_agent' => (string) $request->userAgent(),
                'created_at' => now(),
            ]);
        } catch (\Throwable) {
        }
    }
}
