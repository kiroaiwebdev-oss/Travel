<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cashback;
use App\Services\Cashback\CashbackService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Admin control over the cashback ledger: monitor every cashback and manually
 * confirm / reject / mature it (e.g. when reconciling provider reports).
 */
class CashbackController extends Controller
{
    public function __construct(private readonly CashbackService $cashback) {}

    public function index(Request $request): View
    {
        $cashbacks = Cashback::query()
            ->with(['user:id,name,email', 'provider:id,name'])
            ->when($request->query('status'), fn ($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.cashbacks.index', [
            'cashbacks' => $cashbacks,
            'totals' => [
                'pending' => (float) Cashback::where('status', 'pending')->sum('amount'),
                'confirmed' => (float) Cashback::where('status', 'confirmed')->sum('amount'),
                'withdrawable' => (float) Cashback::where('status', 'withdrawable')->sum('amount'),
                'rejected' => (float) Cashback::where('status', 'rejected')->sum('amount'),
            ],
        ]);
    }

    public function confirm(Cashback $cashback): RedirectResponse
    {
        $this->cashback->confirm($cashback);

        return back()->with('status', "Cashback #{$cashback->id} confirmed.");
    }

    public function mature(Cashback $cashback): RedirectResponse
    {
        $this->cashback->mature($cashback);

        return back()->with('status', "Cashback #{$cashback->id} released to withdrawable.");
    }

    public function reject(Request $request, Cashback $cashback): RedirectResponse
    {
        $data = $request->validate(['reason' => ['nullable', 'string', 'max:255']]);
        $this->cashback->reject($cashback, $data['reason'] ?? 'Rejected by admin');

        return back()->with('status', "Cashback #{$cashback->id} rejected & reversed.");
    }
}
