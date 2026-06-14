<?php

namespace App\Services\Providers;

use App\Contracts\ProviderAdapter;
use App\DTO\NormalizedOffer;
use App\DTO\SearchQuery;
use App\Models\Provider;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Shared behaviour for all adapters: config binding, category support, a guarded
 * outbound HTTP client (SSRF allow-list), deep-link building, and a demo-offer
 * generator so the platform is fully functional before real API keys are added.
 */
abstract class AbstractProviderAdapter implements ProviderAdapter
{
    protected Provider $provider;

    /** @var array<string,mixed> */
    protected array $config = [];

    public function for(Provider $provider, array $config): static
    {
        $this->provider = $provider;
        $this->config = $config;

        return $this;
    }

    public function supports(string $category): bool
    {
        return $this->provider->supports($category);
    }

    public function search(SearchQuery $query): array
    {
        if (! $this->supports($query->category)) {
            return [];
        }

        // No live credentials yet -> serve realistic demo offers so the funnel works.
        if ($this->isDemoMode()) {
            return $this->demoOffers($query);
        }

        try {
            $response = $this->fetch($query);

            return $this->normalize($response, $query);
        } catch (\Throwable $e) {
            // A broken provider must degrade gracefully, never break search.
            Log::warning('Provider search failed', [
                'provider' => $this->provider->slug,
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /** Perform the live API call. Override fetch() OR normalize() per provider. */
    protected function fetch(SearchQuery $query): array
    {
        $path = (string) ($this->config['search_path'] ?? '/search');

        return $this->client()
            ->get(rtrim((string) $this->config['base_url'], '/').$path, $query->toArray())
            ->throw()
            ->json() ?? [];
    }

    /**
     * Translate the raw provider response into NormalizedOffer[].
     * Default expects a generic {"results":[...]} envelope.
     *
     * @return array<int, NormalizedOffer>
     */
    abstract protected function normalize(array $raw, SearchQuery $query): array;

    protected function client(): PendingRequest
    {
        $http = config('providers.http');

        return Http::timeout($http['timeout'])
            ->connectTimeout($http['connect_timeout'])
            ->retry($http['retries'], 200)
            ->withHeaders($this->authHeaders())
            ->beforeSending(function ($request) {
                $this->assertHostAllowed($request->url());
            });
    }

    protected function authHeaders(): array
    {
        $headers = ['Accept' => 'application/json'];
        if (! empty($this->config['api_key'])) {
            $headers['Authorization'] = 'Bearer '.$this->config['api_key'];
        }

        return $headers;
    }

    /** SSRF guard (OWASP A10). */
    protected function assertHostAllowed(string $url): void
    {
        $allowed = config('providers.allowed_hosts', []);
        if (empty($allowed)) {
            return; // not configured -> allow (dev)
        }
        $host = parse_url($url, PHP_URL_HOST);
        if ($host && ! in_array($host, $allowed, true)) {
            throw new \RuntimeException("Outbound host not allowed: {$host}");
        }
    }

    public function buildBookUrl(NormalizedOffer $offer, string $clickId): string
    {
        $template = $this->provider->tracking_template;
        $target = $offer->bookUrl ?? ('https://'.($this->config['host'] ?? $this->provider->slug.'.com'));

        if (! $template) {
            return $target;
        }

        return strtr($template, [
            '{host}' => $this->config['host'] ?? ($this->provider->slug.'.com'),
            '{click_id}' => $clickId,
            '{target}' => urlencode($target),
            '{offer_ref}' => (string) $offer->offerRef,
        ]);
    }

    protected function isDemoMode(): bool
    {
        return (bool) ($this->config['demo_mode'] ?? false)
            || empty($this->config['base_url']);
    }

    /**
     * Deterministic-ish sample offers, varied by provider so the UI looks real.
     *
     * @return array<int, NormalizedOffer>
     */
    protected function demoOffers(SearchQuery $query): array
    {
        $factory = new DemoOfferFactory($this->provider);

        return $factory->make($query);
    }
}
