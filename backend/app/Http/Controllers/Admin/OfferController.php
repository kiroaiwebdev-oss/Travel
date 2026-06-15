<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use App\Models\Provider;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    public function index(): View
    {
        return view('admin.offers.index', [
            'offers' => Offer::with('provider')->orderBy('sort_order')->latest()->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('admin.offers.form', [
            'offer' => new Offer(['cashback_type' => 'percentage', 'is_active' => true, 'sort_order' => 0]),
            'providers' => Provider::orderBy('name')->get(),
            'categories' => config('travelcash.categories'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Offer::create($this->validated($request));

        return redirect()->route('admin.offers.index')->with('status', 'Offer created.');
    }

    public function edit(Offer $offer): View
    {
        return view('admin.offers.form', [
            'offer' => $offer,
            'providers' => Provider::orderBy('name')->get(),
            'categories' => config('travelcash.categories'),
        ]);
    }

    public function update(Request $request, Offer $offer): RedirectResponse
    {
        $offer->update($this->validated($request));

        return redirect()->route('admin.offers.index')->with('status', 'Offer updated.');
    }

    public function destroy(Offer $offer): RedirectResponse
    {
        $offer->delete();

        return back()->with('status', 'Offer deleted.');
    }

    public function toggle(Offer $offer): RedirectResponse
    {
        $offer->update(['is_active' => ! $offer->is_active]);

        return back()->with('status', 'Offer '.($offer->is_active ? 'activated' : 'paused').'.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'provider_id' => ['nullable', 'exists:providers,id'],
            'category' => ['required', 'string', 'in:'.implode(',', array_keys(config('travelcash.categories')))],
            'cashback_label' => ['nullable', 'string', 'max:120'],
            'cashback_type' => ['required', 'in:percentage,flat'],
            'cashback_value' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:2000'],
            'terms' => ['nullable', 'string', 'max:2000'],
            'image_url' => ['nullable', 'url'],
            'deep_link' => ['nullable', 'string', 'max:1024'],
            'is_featured' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'expires_at' => ['nullable', 'date'],
        ]);
    }
}
