<?php

namespace App\Services\Providers;

use App\Contracts\ProviderAdapter;
use App\Models\Provider;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * Registry + factory for provider adapters. Resolves the correct adapter class
 * for a provider's "driver", binds it to the provider's decrypted config, and
 * lists the active providers for a category (priority-ordered). New providers
 * added in the DB become active here instantly — no redeploy.
 */
class ProviderManager
{
    /** @var array<string,string> driver => adapter class */
    private array $registry;

    public function __construct()
    {
        $this->registry = config('providers.adapters', []);
    }

    /** Build a ready-to-use adapter bound to a provider + its config. */
    public function adapterFor(Provider $provider): ProviderAdapter
    {
        $driver = $provider->adapter ?: config('providers.default_adapter', 'generic_rest');
        $class = $this->registry[$driver]
            ?? $this->registry[config('providers.default_adapter')]
            ?? GenericRestAdapter::class;

        /** @var ProviderAdapter $adapter */
        $adapter = app($class);

        $config = $provider->activeConfiguration?->config ?? [];

        return $adapter->for($provider, $config);
    }

    /**
     * Active providers that serve the given category, priority-ordered.
     * Cached briefly so search fan-out stays fast.
     *
     * @return Collection<int, Provider>
     */
    public function activeFor(string $category): Collection
    {
        return Cache::remember(
            "providers:active:{$category}",
            60,
            fn () => Provider::query()
                ->active()
                ->forCategory($category)
                ->with('activeConfiguration')
                ->orderBy('priority')
                ->get()
        );
    }

    /** All registered driver keys (for the admin UI dropdown). */
    public function availableDrivers(): array
    {
        return array_keys($this->registry);
    }

    /** Flush the active-provider cache (call after admin edits a provider). */
    public function flushCache(): void
    {
        foreach (array_keys(config('travelcash.categories', [])) as $category) {
            Cache::forget("providers:active:{$category}");
        }
    }
}
