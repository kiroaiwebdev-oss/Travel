<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Withdrawal;
use App\Services\Wallet\WalletService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class WithdrawalController extends Controller
{
    public function __construct(private readonly WalletService $wallet) {}

    public function index(Request $request): View
    {
        return view('dashboard.withdrawals', [
            'wallet' => $this->wallet->walletFor($request->user()),
            'withdrawals' => $request->user()->withdrawals()->latest()->paginate(15),
            'min' => (float) config('tripcash.cashback.min_withdrawal', 500),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $min = (float) config('tripcash.cashback.min_withdrawal', 500);
        $data = $request->validate([
            'amount' => ['required', 'numeric', "min:{$min}"],
            'method' => ['required', 'in:upi,bank,paypal,voucher'],
            'payout_details' => ['required', 'array'],
        ]);

        $user = $request->user();

        if (! $user->isKycApproved()) {
            return back()->withErrors(['amount' => 'Complete your KYC verification before withdrawing.']);
        }

        $wallet = $this->wallet->walletFor($user);

        if ((float) $wallet->balance < (float) $data['amount']) {
            throw ValidationException::withMessages(['amount' => 'Amount exceeds your withdrawable balance.']);
        }

        // Debit immediately (held), admin then approves/pays out.
        $this->wallet->debit(
            user: $user,
            amount: (float) $data['amount'],
            type: 'withdrawal_debit',
            description: 'Withdrawal request',
        );

        $user->withdrawals()->create([
            'amount' => $data['amount'],
            'currency' => $wallet->currency,
            'method' => $data['method'],
            'payout_details' => $data['payout_details'],
            'status' => Withdrawal::REQUESTED,
        ]);

        return back()->with('status', 'Withdrawal requested. We will process it shortly.');
    }
}
