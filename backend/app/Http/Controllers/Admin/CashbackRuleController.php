<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashbackRule;
use App\Models\Provider;
use App\Services\Cashback\CashbackRuleResolver;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CashbackRuleController extends Controller
{
    public function index(): View
    {
        return view('admin.cashback.index', [
            'rules' => CashbackRule::with('provider')->orderBy('priority')->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('admin.cashback.form', [
            'rule' => new CashbackRule(['type' => 'percentage', 'priority' => 100, 'is_active' => true]),
            'providers' => Provider::orderBy('name')->get(),
            'categories' => config('travelcash.categories'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        CashbackRule::create($this->validated($request));
        CashbackRuleResolver::flushCache();

        return redirect()->route('admin.cashback-rules.index')->with('status', 'Cashback rule created.');
    }

    public function edit(CashbackRule $cashbackRule): View
    {
        return view('admin.cashback.form', [
            'rule' => $cashbackRule,
            'providers' => Provider::orderBy('name')->get(),
            'categories' => config('travelcash.categories'),
        ]);
    }

    public function update(Request $request, CashbackRule $cashbackRule): RedirectResponse
    {
        $cashbackRule->update($this->validated($request));
        CashbackRuleResolver::flushCache();

        return redirect()->route('admin.cashback-rules.index')->with('status', 'Cashback rule updated.');
    }

    public function destroy(CashbackRule $cashbackRule): RedirectResponse
    {
        $cashbackRule->delete();
        CashbackRuleResolver::flushCache();

        return back()->with('status', 'Rule deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'provider_id' => ['nullable', 'exists:providers,id'],
            'category' => ['nullable', 'string', 'max:20'],
            'type' => ['required', 'in:percentage,fixed'],
            'value' => ['required', 'numeric', 'min:0'],
            'max_cap' => ['nullable', 'numeric', 'min:0'],
            'min_booking_amount' => ['nullable', 'numeric', 'min:0'],
            'priority' => ['required', 'integer', 'min:1'],
            'is_active' => ['nullable', 'boolean'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
        ]);
    }
}
