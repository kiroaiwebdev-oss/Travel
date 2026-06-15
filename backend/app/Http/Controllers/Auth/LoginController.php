<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\UserDevice;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function show(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => __('These credentials do not match our records.'),
            ]);
        }

        // Hard separation: admins must use the dedicated admin login, never this one.
        if (Auth::user()->hasPermission('admin.access')) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            throw ValidationException::withMessages([
                'email' => 'Admin accounts must sign in via the admin panel at /admin/login.',
            ]);
        }

        $request->session()->regenerate();

        $user = Auth::user();
        $user->forceFill(['last_login_at' => now(), 'last_login_ip' => $request->ip()])->save();
        $this->trackDevice($request);

        return redirect()->intended(route('dashboard.index'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    private function trackDevice(Request $request): void
    {
        $hash = hash('sha256', $request->ip().'|'.$request->userAgent());
        UserDevice::updateOrCreate(
            ['user_id' => Auth::id(), 'device_hash' => $hash],
            [
                'device_name' => substr((string) $request->userAgent(), 0, 120),
                'platform' => $request->header('Sec-CH-UA-Platform'),
                'ip_address' => $request->ip(),
                'last_used_at' => now(),
            ]
        );
    }
}
