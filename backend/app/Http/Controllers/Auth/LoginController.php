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

        // Hard separation WITHOUT account enumeration: admins cannot use the public
        // login. We reject with the SAME generic message (never reveal it's an admin).
        if (Auth::user()->hasPermission('admin.access')) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            throw ValidationException::withMessages([
                'email' => __('These credentials do not match our records.'),
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
