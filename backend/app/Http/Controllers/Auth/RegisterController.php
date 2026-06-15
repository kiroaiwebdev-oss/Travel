<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Services\Referral\ReferralService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function __construct(private readonly ReferralService $referrals) {}

    public function show(Request $request): View
    {
        return view('auth.register', ['ref' => $request->query('ref')]);
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
            'ref' => ['nullable', 'string', 'max:12'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'], // hashed once by the model cast
        ]);
        $user->roles()->attach(Role::where('name', 'user')->value('id'));

        // Attribute the referral if a valid code was supplied.
        if (! empty($data['ref'])) {
            $this->referrals->attachReferral($user, $data['ref'], $request);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard.index');
    }
}
