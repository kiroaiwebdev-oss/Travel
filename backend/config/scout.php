<?php

return [
    'driver' => env('SCOUT_DRIVER', 'meilisearch'),
    'prefix' => env('SCOUT_PREFIX', 'tc_'),
    'queue' => env('SCOUT_QUEUE', true),
    'after_commit' => false,
    'chunk' => [
        'searchable' => 500,
        'unsearchable' => 500,
    ],
    'soft_delete' => false,
    'identify' => env('SCOUT_IDENTIFY', false),

    'meilisearch' => [
        'host' => env('MEILISEARCH_HOST', 'http://meilisearch:7700'),
        'key' => env('MEILISEARCH_KEY'),
        'index-settings' => [
            // Cached, normalized travel offers indexed for instant search.
            'offers' => [
                'filterableAttributes' => [
                    'category', 'provider_slug', 'origin', 'destination',
                    'city', 'price', 'rating', 'stops', 'cashback',
                ],
                'sortableAttributes' => ['price', 'cashback', 'rating', 'duration_minutes'],
            ],
        ],
    ],
];
