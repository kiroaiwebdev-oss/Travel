<?php

use App\Services\Providers\Adapters\BookingComAdapter;
use App\Services\Providers\Adapters\GenericRestAdapter;
use App\Services\Providers\Adapters\MakeMyTripAdapter;

return [
    /*
    |--------------------------------------------------------------------------
    | Provider Adapter Registry
    |--------------------------------------------------------------------------
    | Maps an adapter "driver" key to the PHP class that implements
    | App\Contracts\ProviderAdapter. A provider row in the database references
    | one of these drivers. New providers added via the Admin panel that use an
    | existing driver (e.g. "generic_rest") become active INSTANTLY with no code
    | change and no redeploy — their API keys/endpoints live in the DB.
    |
    | To support a brand-new API shape, add one adapter class + one line here.
    */
    'adapters' => [
        'generic_rest' => GenericRestAdapter::class,
        'booking_com'  => BookingComAdapter::class,
        'makemytrip'   => MakeMyTripAdapter::class,
    ],

    // Default adapter used when a provider does not specify one.
    'default_adapter' => 'generic_rest',

    // Outbound HTTP guard: only these hosts may be called by adapters (SSRF, A10).
    // Empty array = allow all (dev). Populate in production.
    'allowed_hosts' => array_filter(explode(',', env('TC_PROVIDER_ALLOWED_HOSTS', ''))),

    'http' => [
        'timeout' => (int) env('TC_PROVIDER_HTTP_TIMEOUT', 8),
        'connect_timeout' => (int) env('TC_PROVIDER_CONNECT_TIMEOUT', 4),
        'retries' => (int) env('TC_PROVIDER_HTTP_RETRIES', 1),
    ],
];
