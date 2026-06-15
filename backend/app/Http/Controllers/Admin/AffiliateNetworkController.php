<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AffiliateNetwork;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AffiliateNetworkController extends Controller
{
    public function index(): View
    {
        return view('admin.networks.index', [
            'networks' => AffiliateNetwork::withCount('providers')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:120']]);
        AffiliateNetwork::create([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'postback_secret' => Str::random(40),
            'is_active' => true,
        ]);

        return back()->with('status', 'Network added with a fresh postback secret.');
    }

    public function update(Request $request, AffiliateNetwork $network): RedirectResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:120']]);
        $network->update($data);

        return back()->with('status', 'Network updated.');
    }

    public function toggle(AffiliateNetwork $network): RedirectResponse
    {
        $network->update(['is_active' => ! $network->is_active]);

        return back()->with('status', 'Network '.($network->is_active ? 'enabled' : 'disabled').'.');
    }

    public function regenerateSecret(AffiliateNetwork $network): RedirectResponse
    {
        $network->update(['postback_secret' => Str::random(40)]);

        return back()->with('status', 'Postback secret regenerated — update it in the network dashboard.');
    }
}
