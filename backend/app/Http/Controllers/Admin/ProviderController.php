<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AffiliateNetwork;
use App\Models\Provider;
use App\Services\Providers\ProviderManager;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProviderController extends Controller
{
    public function __construct(private readonly ProviderManager $manager) {}

    public function index(): View
    {
        return view('admin.providers.index', [
            'providers' => Provider::with('network', 'activeConfiguration')->orderBy('priority')->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('admin.providers.form', [
            'provider' => new Provider(['categories' => [], 'priority' => 100, 'is_active' => true]),
            'networks' => AffiliateNetwork::orderBy('name')->get(),
            'drivers' => $this->manager->availableDrivers(),
            'categories' => config('travelcash.categories'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateProvider($request);
        $data['slug'] = Str::slug($data['name']);
        $provider = Provider::create($data);
        $this->saveConfig($provider, $request);

        return redirect()->route('admin.providers.index')->with('status', "Provider {$provider->name} created and active.");
    }

    public function edit(Provider $provider): View
    {
        return view('admin.providers.form', [
            'provider' => $provider->load('activeConfiguration'),
            'networks' => AffiliateNetwork::orderBy('name')->get(),
            'drivers' => $this->manager->availableDrivers(),
            'categories' => config('travelcash.categories'),
        ]);
    }

    public function update(Request $request, Provider $provider): RedirectResponse
    {
        $provider->update($this->validateProvider($request));
        $this->saveConfig($provider, $request);

        return redirect()->route('admin.providers.index')->with('status', 'Provider updated.');
    }

    public function destroy(Provider $provider): RedirectResponse
    {
        $provider->delete();

        return back()->with('status', 'Provider removed.');
    }

    public function toggle(Provider $provider): RedirectResponse
    {
        $provider->update(['is_active' => ! $provider->is_active]);

        return back()->with('status', 'Provider '.($provider->is_active ? 'activated' : 'paused').'.');
    }

    /** Update only the encrypted API credentials/config. */
    public function updateConfig(Request $request, Provider $provider): RedirectResponse
    {
        $this->saveConfig($provider, $request);

        return back()->with('status', 'Provider configuration saved.');
    }

    private function validateProvider(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'affiliate_network_id' => ['nullable', 'exists:affiliate_networks,id'],
            'adapter' => ['required', 'string', 'in:'.implode(',', $this->manager->availableDrivers())],
            'categories' => ['required', 'array', 'min:1'],
            'categories.*' => ['string', 'in:'.implode(',', array_keys(config('travelcash.categories')))],
            'logo_url' => ['nullable', 'url'],
            'priority' => ['required', 'integer', 'min:1', 'max:9999'],
            'commission_percent' => ['required', 'numeric', 'min:0', 'max:100'],
            'tracking_template' => ['nullable', 'string', 'max:1024'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }

    /**
     * Save provider credentials. Once saved with a base_url, demo mode turns off
     * and live search uses the real API automatically — no redeploy needed.
     */
    private function saveConfig(Provider $provider, Request $request): void
    {
        $config = $request->validate([
            'config.base_url' => ['nullable', 'url'],
            'config.api_key' => ['nullable', 'string', 'max:255'],
            'config.secret_key' => ['nullable', 'string', 'max:255'],
            'config.search_path' => ['nullable', 'string', 'max:255'],
            'config.host' => ['nullable', 'string', 'max:190'],
            'config.demo_mode' => ['nullable', 'boolean'],
        ])['config'] ?? [];

        $config['demo_mode'] = empty($config['base_url']);

        $provider->configurations()->updateOrCreate(
            ['environment' => app()->environment('production') ? 'production' : 'sandbox'],
            ['config' => $config, 'is_active' => true]
        );

        $this->manager->flushCache();
    }
}
