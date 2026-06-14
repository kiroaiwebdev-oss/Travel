<?php

namespace Tests\Feature;

use App\DTO\SearchQuery;
use App\Models\CashbackRule;
use App\Models\Provider;
use App\Services\Providers\ProviderManager;
use App\Services\Search\SearchService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class ProviderPluginSearchTest extends TestCase
{
    use RefreshDatabase;

    private function seedProvider(string $slug, array $categories, float $commission = 8): Provider
    {
        $provider = Provider::create([
            'name' => ucfirst($slug), 'slug' => $slug, 'adapter' => 'generic_rest',
            'categories' => $categories, 'is_active' => true, 'priority' => 10,
            'commission_percent' => $commission,
            'tracking_template' => 'https://{host}/go?subid={click_id}&url={target}',
        ]);
        // Demo-mode config under the env the model resolves in tests ("sandbox").
        $provider->configurations()->create([
            'environment' => 'sandbox',
            'config' => ['demo_mode' => true, 'host' => $slug.'.com'],
            'is_active' => true,
        ]);

        return $provider;
    }

    public function test_active_provider_is_discovered_for_category(): void
    {
        $this->seedProvider('booking-com', ['hotels']);
        Cache::flush();

        $active = app(ProviderManager::class)->activeFor('hotels');

        $this->assertCount(1, $active);
        $this->assertSame('booking-com', $active->first()->slug);
    }

    public function test_search_returns_offers_with_cashback_and_signed_links(): void
    {
        $this->seedProvider('booking-com', ['hotels'], commission: 8);
        $this->seedProvider('agoda', ['hotels'], commission: 7);
        CashbackRule::create(['name' => 'Global', 'type' => 'percentage', 'value' => 40, 'priority' => 1000, 'is_active' => true]);
        Cache::flush();

        $result = app(SearchService::class)->search(SearchQuery::fromArray([
            'category' => 'hotels', 'destination' => 'Goa', 'currency' => 'INR', 'sort' => 'highest_cashback',
        ]));

        $this->assertNotEmpty($result['offers']);
        $first = $result['offers'][0];
        $this->assertSame('hotels', $first['category']);
        $this->assertGreaterThan(0, $first['cashback']);
        $this->assertStringContainsString('/go/', $first['go_url']);
        $this->assertStringContainsString('signature=', $first['go_url']);

        // highest_cashback sort: first cashback >= last cashback
        $last = end($result['offers']);
        $this->assertGreaterThanOrEqual($last['cashback'], $first['cashback']);
    }

    public function test_inactive_provider_is_excluded(): void
    {
        $p = $this->seedProvider('booking-com', ['hotels']);
        $p->update(['is_active' => false]);
        Cache::flush();

        $this->assertCount(0, app(ProviderManager::class)->activeFor('hotels'));
    }
}
