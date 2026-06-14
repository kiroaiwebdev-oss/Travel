<?php

namespace Tests\Unit;

use App\DTO\SearchQuery;
use PHPUnit\Framework\TestCase;

class SearchQueryTest extends TestCase
{
    public function test_from_array_maps_fields(): void
    {
        $q = SearchQuery::fromArray([
            'category' => 'hotels',
            'destination' => 'Goa',
            'travellers' => 3,
            'currency' => 'INR',
        ]);

        $this->assertSame('hotels', $q->category);
        $this->assertSame('Goa', $q->destination);
        $this->assertSame(3, $q->travellers);
    }

    public function test_cache_key_is_deterministic(): void
    {
        $a = SearchQuery::fromArray(['category' => 'hotels', 'destination' => 'Goa', 'currency' => 'INR']);
        $b = SearchQuery::fromArray(['category' => 'hotels', 'destination' => 'Goa', 'currency' => 'INR']);

        $this->assertSame($a->cacheKey(), $b->cacheKey());
    }

    public function test_cache_key_changes_with_inputs(): void
    {
        $a = SearchQuery::fromArray(['category' => 'hotels', 'destination' => 'Goa', 'currency' => 'INR']);
        $b = SearchQuery::fromArray(['category' => 'hotels', 'destination' => 'Bali', 'currency' => 'INR']);

        $this->assertNotSame($a->cacheKey(), $b->cacheKey());
    }
}
