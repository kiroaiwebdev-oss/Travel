<?php

return [
    // Default fiat currency for display + ledger
    'currency' => env('TC_DEFAULT_CURRENCY', 'INR'),

    // Supported search verticals. Adding a vertical here surfaces it across
    // search, cashback rules and admin without touching code elsewhere.
    'categories' => [
        'flights'   => ['label' => 'Flights',   'icon' => 'plane'],
        'hotels'    => ['label' => 'Hotels',    'icon' => 'bed'],
        'trains'    => ['label' => 'Trains',    'icon' => 'train-front'],
        'cabs'      => ['label' => 'Cabs',       'icon' => 'car-taxi-front'],
        'packages'  => ['label' => 'Packages',  'icon' => 'palmtree'],
        'guides'    => ['label' => 'Guides',     'icon' => 'compass'],
        'activities'=> ['label' => 'Activities', 'icon' => 'ticket'],
        'transfers' => ['label' => 'Airport Transfers', 'icon' => 'luggage'],
    ],

    'cashback' => [
        // % of our commission shared back to the user when no specific rule matches
        'default_share_percent' => (float) env('TC_DEFAULT_CASHBACK_PERCENT', 40),
        // Days a confirmed cashback is held before it becomes withdrawable
        'hold_days' => (int) env('TC_CASHBACK_HOLD_DAYS', 30),
        'min_withdrawal' => (float) env('TC_MIN_WITHDRAWAL', 500),
        'states' => ['pending', 'confirmed', 'withdrawable', 'rejected', 'paid'],
    ],

    'affiliate' => [
        'cookie_days' => (int) env('TC_AFFILIATE_COOKIE_DAYS', 30),
        // Click-out links are signed; postbacks are HMAC verified.
        'redirect_route' => 'go',
    ],

    'referral' => [
        'reward_amount' => (float) env('TC_REFERRAL_REWARD', 100),
        // Reward releases only after referee's first confirmed cashback (fraud guard)
        'require_confirmed_booking' => true,
        'max_referrals_per_day' => 20,
    ],

    'search' => [
        // Cache TTL (seconds) for normalized provider results
        'cache_ttl' => (int) env('TC_SEARCH_CACHE_TTL', 300),
        // Hard cap per provider per search to keep latency bounded
        'per_provider_limit' => 50,
    ],
];
