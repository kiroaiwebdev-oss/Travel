<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Wallet\WalletService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(private readonly WalletService $wallet) {}

    public function index(Request $request): View
    {
        $users = User::query()
            ->when($request->query('q'), fn ($q, $term) => $q->where(fn ($w) => $w
                ->where('name', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%")))
            ->with('wallet')
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.users.index', ['users' => $users, 'q' => $request->query('q')]);
    }

    public function show(User $user): View
    {
        return view('admin.users.show', [
            'user' => $user->load('wallet', 'roles', 'bookings.provider'),
            'cashbacks' => $user->cashbacks()->with('provider')->latest()->limit(20)->get(),
        ]);
    }

    public function updateStatus(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate(['status' => ['required', 'in:active,suspended,banned']]);
        $user->update($data);

        return back()->with('status', "User marked {$data['status']}.");
    }

    /** Manual wallet transaction for disputes/goodwill (double-entry ledger). */
    public function adjustWallet(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'direction' => ['required', 'in:credit,debit'],
            'note' => ['required', 'string', 'max:200'],
        ]);

        try {
            if ($data['direction'] === 'credit') {
                $this->wallet->credit($user, (float) $data['amount'], 'adjustment',
                    description: 'Admin adjustment: '.$data['note'],
                    meta: ['by' => $request->user()->id]);
            } else {
                $this->wallet->debit($user, (float) $data['amount'], 'adjustment',
                    description: 'Admin adjustment: '.$data['note'],
                    meta: ['by' => $request->user()->id]);
            }
        } catch (\Throwable $e) {
            return back()->withErrors(['amount' => $e->getMessage()]);
        }

        return back()->with('status', 'Wallet adjusted ('.$data['direction'].' ₹'.$data['amount'].').');
    }
}
