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

    // Razorpay Payouts (UPI/Bank — India)
    'razorpay' => [
        'key' => env('RAZORPAY_KEY'),
        'secret' => env('RAZORPAY_SECRET'),
        'account_number' => env('RAZORPAY_ACCOUNT_NUMBER'),
    ],

    // PayPal Payouts
    'paypal' => [
        'mode' => env('PAYPAL_MODE', 'sandbox'),
        'client_id' => env('PAYPAL_CLIENT_ID'),
        'secret' => env('PAYPAL_SECRET'),
    ],

    // Web Push (VAPID) — browser push notifications
    'webpush' => [
        'public_key' => env('VAPID_PUBLIC_KEY'),
        'private_key' => env('VAPID_PRIVATE_KEY'),
        'subject' => env('VAPID_SUBJECT', 'mailto:admin@tripcash.test'),
    ],

    // Twilio — SMS + WhatsApp (env fallback; admin can override in Integrations)
    'twilio' => [
        'sid' => env('TWILIO_SID'),
        'token' => env('TWILIO_TOKEN'),
        'from' => env('TWILIO_FROM'),                       // SMS sender number
        'whatsapp_from' => env('TWILIO_WHATSAPP_FROM'),     // e.g. whatsapp:+14155238886
    ],

    // WhatsApp Business Cloud API (Meta)
    'whatsapp' => [
        'token' => env('WHATSAPP_TOKEN'),
        'phone_id' => env('WHATSAPP_PHONE_ID'),
    ],
];
