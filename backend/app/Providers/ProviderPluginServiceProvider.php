<?php

namespace App\Providers;

use App\Models\Provider;
use App\Services\Providers\ProviderManager;
use Illuminate\Support\ServiceProvider;

class ProviderPluginServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Singleton registry/factory for provider adapters.
        $this->app->singleton(ProviderManager::class, fn () => new ProviderManager());
    }

    public function boot(): void
    {
        // Keep the active-provider cache fresh: any provider change busts it,
        // so newly added/edited providers appear in search immediately.
        $flush = fn () => app(ProviderManager::class)->flushCache();
        Provider::saved($flush);
        Provider::deleted($flush);
    }
}
