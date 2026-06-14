<?php

namespace Database\Seeders;

use App\Models\AffiliateNetwork;
use App\Models\CashbackRule;
use App\Models\Provider;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProviderSeeder extends Seeder
{
    public function run(): void
    {
        $impact = AffiliateNetwork::updateOrCreate(
            ['slug' => 'impact'],
            ['name' => 'Impact', 'postback_secret' => Str::random(40), 'is_active' => true]
        );
        $cj = AffiliateNetwork::updateOrCreate(
            ['slug' => 'cj'],
            ['name' => 'CJ Affiliate', 'postback_secret' => Str::random(40), 'is_active' => true]
        );

        // Each provider uses the "generic_rest" adapter. Because no live keys exist
        // at install time, configs run in demo mode (sample offers) until an admin
        // enters real API credentials — at which point search uses the live API
        // automatically, with no code change.
        $providers = [
            ['name' => 'Booking.com', 'slug' => 'booking-com', 'adapter' => 'booking_com', 'cats' => ['hotels', 'transfers'], 'commission' => 8.0, 'priority' => 10, 'net' => $impact->id],
            ['name' => 'Agoda', 'slug' => 'agoda', 'adapter' => 'generic_rest', 'cats' => ['hotels'], 'commission' => 7.0, 'priority' => 20, 'net' => $impact->id],
            ['name' => 'Expedia', 'slug' => 'expedia', 'adapter' => 'generic_rest', 'cats' => ['hotels', 'flights', 'packages'], 'commission' => 6.5, 'priority' => 30, 'net' => $cj->id],
            ['name' => 'MakeMyTrip', 'slug' => 'makemytrip', 'adapter' => 'makemytrip', 'cats' => ['flights', 'hotels', 'trains', 'packages'], 'commission' => 5.0, 'priority' => 15, 'net' => $cj->id],
            ['name' => 'Goibibo', 'slug' => 'goibibo', 'adapter' => 'generic_rest', 'cats' => ['flights', 'hotels', 'trains'], 'commission' => 5.0, 'priority' => 25, 'net' => $cj->id],
            ['name' => 'Cleartrip', 'slug' => 'cleartrip', 'adapter' => 'generic_rest', 'cats' => ['flights', 'trains'], 'commission' => 4.5, 'priority' => 35, 'net' => $cj->id],
            ['name' => 'Uber', 'slug' => 'uber', 'adapter' => 'generic_rest', 'cats' => ['cabs', 'transfers'], 'commission' => 3.0, 'priority' => 40, 'net' => $impact->id],
            ['name' => 'Ola', 'slug' => 'ola', 'adapter' => 'generic_rest', 'cats' => ['cabs'], 'commission' => 3.0, 'priority' => 45, 'net' => $impact->id],
            ['name' => 'Tripadvisor', 'slug' => 'tripadvisor', 'adapter' => 'generic_rest', 'cats' => ['guides', 'activities', 'hotels'], 'commission' => 6.0, 'priority' => 50, 'net' => $impact->id],
        ];

        foreach ($providers as $p) {
            $provider = Provider::updateOrCreate(
                ['slug' => $p['slug']],
                [
                    'affiliate_network_id' => $p['net'],
                    'name' => $p['name'],
                    'adapter' => $p['adapter'],
                    'categories' => $p['cats'],
                    'is_active' => true,
                    'priority' => $p['priority'],
                    'commission_percent' => $p['commission'],
                    'logo_url' => "https://logo.clearbit.com/{$p['slug']}.com",
                    'tracking_template' => 'https://{host}/deeplink?aff=travelcash&subid={click_id}&url={target}',
                ]
            );

            $provider->configurations()->updateOrCreate(
                ['environment' => 'production'],
                [
                    'config' => [
                        'demo_mode' => true,         // sample offers until real keys added
                        'base_url' => null,
                        'api_key' => null,
                        'secret_key' => null,
                        'search_path' => '/search',
                        'host' => "{$p['slug']}.com",
                    ],
                    'is_active' => true,
                ]
            );
        }

        // --- Cashback rules (most specific wins) ---
        // Global default: share 40% of commission as percentage cashback.
        CashbackRule::updateOrCreate(
            ['name' => 'Global default'],
            ['provider_id' => null, 'category' => null, 'type' => 'percentage', 'value' => 40, 'priority' => 1000, 'is_active' => true]
        );
        // Hotels get a sweeter 60% share.
        CashbackRule::updateOrCreate(
            ['name' => 'Hotels boost'],
            ['provider_id' => null, 'category' => 'hotels', 'type' => 'percentage', 'value' => 60, 'priority' => 500, 'is_active' => true]
        );
        // MakeMyTrip flights: flat ₹250 cashback.
        $mmt = Provider::where('slug', 'makemytrip')->first();
        if ($mmt) {
            CashbackRule::updateOrCreate(
                ['name' => 'MMT flights flat'],
                ['provider_id' => $mmt->id, 'category' => 'flights', 'type' => 'fixed', 'value' => 250, 'priority' => 100, 'is_active' => true, 'max_cap' => 250]
            );
        }
    }
}
