<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\JsonResponse;

/**
 * Serves the PWA manifest dynamically so the installed-app icon + name reflect
 * the branding an admin configures in Settings (site.icon / site.name).
 */
class ManifestController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $icon = Setting::get('site.icon');
        $name = (string) Setting::get('site.name', config('app.name', 'TripCash'));

        $icons = $icon
            ? [
                ['src' => $icon, 'sizes' => 'any', 'type' => $this->mime($icon), 'purpose' => 'any'],
                ['src' => $icon, 'sizes' => '192x192', 'type' => $this->mime($icon), 'purpose' => 'maskable'],
                ['src' => $icon, 'sizes' => '512x512', 'type' => $this->mime($icon), 'purpose' => 'maskable'],
            ]
            : [
                ['src' => '/icon.svg', 'sizes' => 'any', 'type' => 'image/svg+xml', 'purpose' => 'any'],
                ['src' => '/icons/icon-192.png', 'sizes' => '192x192', 'type' => 'image/png', 'purpose' => 'any maskable'],
                ['src' => '/icons/icon-512.png', 'sizes' => '512x512', 'type' => 'image/png', 'purpose' => 'any maskable'],
            ];

        return response()->json([
            'name' => $name.' — Cashback on every trip',
            'short_name' => $name,
            'description' => (string) Setting::get('seo.meta_description', 'Compare flights, hotels, trains, cabs & packages and earn real cashback into your wallet.'),
            'start_url' => '/?source=pwa',
            'scope' => '/',
            'display' => 'standalone',
            'display_override' => ['standalone', 'minimal-ui'],
            'orientation' => 'portrait',
            'background_color' => '#F8FAFC',
            'theme_color' => '#0F62FE',
            'lang' => 'en',
            'dir' => 'ltr',
            'categories' => ['travel', 'finance', 'shopping'],
            'icons' => $icons,
            'shortcuts' => [
                ['name' => 'Search hotels', 'short_name' => 'Hotels', 'url' => '/search?category=hotels'],
                ['name' => 'Search flights', 'short_name' => 'Flights', 'url' => '/search?category=flights'],
                ['name' => 'My wallet', 'short_name' => 'Wallet', 'url' => '/dashboard/wallet'],
            ],
        ])->header('Content-Type', 'application/manifest+json');
    }

    private function mime(string $path): string
    {
        return match (strtolower(pathinfo($path, PATHINFO_EXTENSION))) {
            'svg' => 'image/svg+xml',
            'jpg', 'jpeg' => 'image/jpeg',
            'webp' => 'image/webp',
            'ico' => 'image/x-icon',
            default => 'image/png',
        };
    }
}
