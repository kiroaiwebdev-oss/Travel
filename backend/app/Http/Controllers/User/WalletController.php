<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\Wallet\WalletService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function __construct(private readonly WalletService $wallet) {}

    public function index(Request $request): View
    {
        $user = $request->user();

        return view('dashboard.wallet', [
            'wallet' => $this->wallet->walletFor($user),
            'transactions' => $user->walletTransactions()->latest()->paginate(20),
        ]);
    }

    public function cashback(Request $request): View
    {
        $user = $request->user();

        return view('dashboard.cashback', [
            'cashbacks' => $user->cashbacks()->with('provider')->latest()->paginate(20),
            'totals' => [
                'pending' => $user->cashbacks()->where('status', 'pending')->sum('amount'),
                'confirmed' => $user->cashbacks()->where('status', 'confirmed')->sum('amount'),
                'withdrawable' => $user->cashbacks()->where('status', 'withdrawable')->sum('amount'),
                'rejected' => $user->cashbacks()->where('status', 'rejected')->sum('amount'),
            ],
        ]);
    }
}
