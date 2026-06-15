<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserDevice;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

/**
 * Passwordless email OTP login. The 6-digit code is cached (10 min, max 5 tries).
 * In production wire an email/SMS provider in send(); for the demo the code is
 * logged and shown on screen so it can be tested without an SMTP/SMS gateway.
 */
class OtpController extends Controller
{
    private function key(string $email): string
    {
        return 'otp:'.sha1(strtolower($email));
    }

    public function showRequest(): View
    {
        return view('auth.otp-request');
    }

    public function send(Request $request): RedirectResponse
    {
        $data = $request->validate(['email' => ['required', 'email']]);
        $email = strtolower($data['email']);

        $user = User::where('email', $email)->first();
        // Don't reveal whether the account exists (anti-enumeration).
        if ($user && $user->status === 'active' && ! $user->hasPermission('admin.access')) {
            $code = (string) random_int(100000, 999999);
            Cache::put($this->key($email), ['hash' => Hash::make($code), 'tries' => 0], now()->addMinutes(10));

            // Deliver via the admin-selected channel (email / SMS / WhatsApp).
            $result = app(\App\Services\Messaging\MessagingService::class)->sendOtp($user, $code);
            Log::info('OTP issued', ['email' => $email, 'channel' => $result['channel'] ?? 'email', 'sent' => $result['ok'] ?? false]);

            if (! app()->environment('production')) {
                session()->flash('otp_demo', $code); // visible only outside production
            }
        }

        return redirect()->route('login.otp.verify.show')
            ->with('otp_email', $email)
            ->with('status', 'If an account exists, a 6-digit code has been sent.');
    }

    public function showVerify(Request $request): View|RedirectResponse
    {
        if (! session('otp_email')) {
            return redirect()->route('login.otp');
        }
        session()->keep(['otp_email', 'otp_demo']);

        return view('auth.otp-verify', ['email' => session('otp_email')]);
    }

    public function verify(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'code' => ['required', 'string', 'size:6'],
        ]);
        $email = strtolower($data['email']);
        $key = $this->key($email);
        $entry = Cache::get($key);

        if (! $entry || $entry['tries'] >= 5 || ! Hash::check($data['code'], $entry['hash'])) {
            if ($entry) {
                $entry['tries']++;
                Cache::put($key, $entry, now()->addMinutes(10));
            }
            throw ValidationException::withMessages(['code' => 'Invalid or expired code.']);
        }

        Cache::forget($key);
        $user = User::where('email', $email)->firstOrFail();

        if ($user->hasPermission('admin.access')) {
            throw ValidationException::withMessages(['code' => 'These credentials do not match our records.']);
        }

        Auth::login($user, true);
        $request->session()->regenerate();
        $user->forceFill(['last_login_at' => now(), 'last_login_ip' => $request->ip(), 'email_verified_at' => $user->email_verified_at ?? now()])->save();

        UserDevice::updateOrCreate(
            ['user_id' => $user->id, 'device_hash' => hash('sha256', $request->ip().'|'.$request->userAgent())],
            ['device_name' => substr((string) $request->userAgent(), 0, 120), 'ip_address' => $request->ip(), 'last_used_at' => now()]
        );

        return redirect()->route('dashboard.index');
    }
}
