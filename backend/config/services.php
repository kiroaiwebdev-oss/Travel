<?php

return [
    // Laravel Scout / Meilisearch
    'meilisearch' => [
        'host' => env('MEILISEARCH_HOST', 'http://meilisearch:7700'),
        'key' => env('MEILISEARCH_KEY'),
    ],

    // Google OAuth (Socialite)
    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),
    ],

    // AI sidecar (FastAPI)
    'ai' => [
        'base_url' => env('AI_SERVICE_URL', 'http://ai:8001'),
        'timeout' => (int) env('AI_SERVICE_TIMEOUT', 30),
    ],
];
