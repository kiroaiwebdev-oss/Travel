<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Withdrawal;
use App\Services\Payout\PayoutManager;
use App\Services\Wallet\WalletService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    public function __construct(
        private readonly WalletService $wallet,
        private readonly PayoutManager $payouts,
    ) {}

    public function index(Request $request): View
    {
        return view('admin.withdrawals.index', [
            'withdrawals' => Withdrawal::with('user')
                ->when($request->query('status'), fn ($q, $s) => $q->where('status', $s))
                ->latest()->paginate(25)->withQueryString(),
            'gateways' => $this->payouts->availableGateways(),
        ]);
    }

    /** Send the payout through a payment gateway (Razorpay/PayPal) or mark manual. */
    public function process(Request $request, Withdrawal $withdrawal): RedirectResponse
    {
        $data = $request->validate(['gateway' => ['required', 'in:manual,razorpay,paypal']]);

        if (! in_array($withdrawal->status, [Withdrawal::REQUESTED, Withdrawal::APPROVED], true)) {
            return back()->withErrors(['gateway' => 'This withdrawal cannot be processed in its current state.']);
        }

        $result = $this->payouts->process($withdrawal, $data['gateway']);
        $withdrawal->update(['processed_by' => $request->user()->id, 'processed_at' => now()]);

        if (! $result['ok']) {
            return back()->withErrors(['gateway' => $result['raw']['error'] ?? 'Payout failed at the gateway.']);
        }

        return back()->with('status', "Payout initiated via {$data['gateway']} (ref: {$result['reference']}).");
    }

    /** Mark a (manual/processing) payout as fully paid. */
    public function approve(Request $request, Withdrawal $withdrawal): RedirectResponse
    {
        $data = $request->validate(['reference' => ['nullable', 'string', 'max:120']]);

        $withdrawal->update([
            'status' => Withdrawal::PAID,
            'reference' => $data['reference'] ?? $withdrawal->reference,
            'processed_by' => $request->user()->id,
            'processed_at' => now(),
        ]);

        return back()->with('status', 'Withdrawal marked as paid.');
    }

    public function reject(Request $request, Withdrawal $withdrawal): RedirectResponse
    {
        $data = $request->validate(['admin_note' => ['required', 'string', 'max:255']]);

        // Refund the held amount back to the user's withdrawable balance.
        if (in_array($withdrawal->status, [Withdrawal::REQUESTED, Withdrawal::APPROVED, Withdrawal::PROCESSING], true)) {
            $this->wallet->credit(
                user: $withdrawal->user,
                amount: (float) $withdrawal->amount,
                type: 'withdrawal_reversal',
                source: $withdrawal,
                idempotencyKey: 'wd_reverse_'.$withdrawal->id,
                description: 'Withdrawal rejected — refund',
            );
        }

        $withdrawal->update([
            'status' => Withdrawal::REJECTED,
            'admin_note' => $data['admin_note'],
            'processed_by' => $request->user()->id,
            'processed_at' => now(),
        ]);

        return back()->with('status', 'Withdrawal rejected and amount refunded.');
    }
}
