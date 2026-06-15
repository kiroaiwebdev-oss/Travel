<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\Wallet\WalletService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    public function __construct(private readonly WalletService $wallet) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        $wallet = $this->wallet->walletFor($user);

        return view('dashboard.index', [
            'wallet' => $wallet,
            'recentCashback' => $user->cashbacks()->with('provider')->latest()->limit(5)->get(),
            'recentBookings' => $user->bookings()->with('provider')->latest()->limit(5)->get(),
            'stats' => [
                'lifetime' => $wallet->lifetime_earned,
                'pending' => $wallet->pending_balance,
                'withdrawable' => $wallet->balance,
                'bookings' => $user->bookings()->count(),
            ],
        ]);
    }

    public function bookings(Request $request): View
    {
        return view('dashboard.bookings', [
            'bookings' => $request->user()->bookings()->with('provider')->latest()->paginate(15),
        ]);
    }

    public function referrals(Request $request): View
    {
        $user = $request->user();

        return view('dashboard.referrals', [
            'code' => $user->referral_code,
            'link' => route('register', ['ref' => $user->referral_code]),
            'referrals' => $user->referralsMade()->with('referee:id,name,email')->latest()->paginate(15),
            'earned' => $user->referralsMade()->where('status', 'rewarded')->sum('reward_amount'),
        ]);
    }

    public function notifications(Request $request): View
    {
        return view('dashboard.notifications', [
            'notifications' => $request->user()->notifications()->latest()->paginate(20),
        ]);
    }

    public function profile(Request $request): View
    {
        return view('dashboard.profile', ['user' => $request->user()]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:32'],
            'currency' => ['nullable', 'string', 'max:8'],
            'locale' => ['nullable', 'string', 'max:8'],
        ]);

        $request->user()->update($data);

        return back()->with('status', 'Profile updated.');
    }

    public function updateAvatar(Request $request): RedirectResponse
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $user = $request->user();

        // Remove the previous uploaded avatar (only if it lived on our public disk).
        if ($user->avatar_url && str_starts_with($user->avatar_url, '/storage/avatars/')) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $user->avatar_url));
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        $user->update(['avatar_url' => '/storage/'.$path]);

        return back()->with('status', 'Profile photo updated.');
    }
}
