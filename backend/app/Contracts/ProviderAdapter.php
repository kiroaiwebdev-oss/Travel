<?php

namespace App\Contracts;

use App\DTO\NormalizedOffer;
use App\DTO\SearchQuery;
use App\Models\Provider;

/**
 * Contract every provider integration must implement. The ProviderManager builds
 * an adapter per active provider; adding a new provider that reuses an existing
 * adapter "driver" requires NO code change — only DB rows.
 */
interface ProviderAdapter
{
    /** Bind the adapter to a concrete provider + its (decrypted) configuration. */
    public function for(Provider $provider, array $config): static;

    /** Does this provider serve the given category right now? */
    public function supports(string $category): bool;

    /**
     * Execute a search and return normalized offers.
     *
     * @return array<int, NormalizedOffer>
     */
    public function search(SearchQuery $query): array;

    /** Build the affiliate deep-link the user is redirected to (with our click id). */
    public function buildBookUrl(NormalizedOffer $offer, string $clickId): string;
}
