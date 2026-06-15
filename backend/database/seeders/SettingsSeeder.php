<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'site.name', 'group' => 'general', 'value' => 'TripCash', 'type' => 'string', 'is_public' => true],
            ['key' => 'site.tagline', 'group' => 'general', 'value' => 'Travel more. Pay less. Earn cashback on every trip.', 'type' => 'string', 'is_public' => true],
            ['key' => 'site.support_email', 'group' => 'general', 'value' => 'support@tripcash.test', 'type' => 'string', 'is_public' => true],
            ['key' => 'site.logo', 'group' => 'branding', 'value' => '', 'type' => 'string', 'is_public' => true],
            ['key' => 'site.icon', 'group' => 'branding', 'value' => '', 'type' => 'string', 'is_public' => true],
            ['key' => 'cashback.default_share_percent', 'group' => 'cashback', 'value' => '40', 'type' => 'float', 'is_public' => true],
            ['key' => 'cashback.hold_days', 'group' => 'cashback', 'value' => '30', 'type' => 'int', 'is_public' => false],
            ['key' => 'cashback.min_withdrawal', 'group' => 'cashback', 'value' => '500', 'type' => 'float', 'is_public' => true],
            ['key' => 'referral.reward_amount', 'group' => 'referral', 'value' => '100', 'type' => 'float', 'is_public' => true],
            ['key' => 'seo.meta_description', 'group' => 'seo', 'value' => 'Compare flights, hotels, trains, cabs & packages. Book through TripCash and earn real cashback.', 'type' => 'string', 'is_public' => true],

            // Homepage banner (admin-controlled promotional strip on the landing page)
            ['key' => 'home.banner_enabled', 'group' => 'homepage', 'value' => '0', 'type' => 'bool', 'is_public' => true],
            ['key' => 'home.banner_title', 'group' => 'homepage', 'value' => 'Monsoon Sale is live!', 'type' => 'string', 'is_public' => true],
            ['key' => 'home.banner_subtitle', 'group' => 'homepage', 'value' => 'Extra cashback on hotels & flights this week only.', 'type' => 'string', 'is_public' => true],
            ['key' => 'home.banner_cta', 'group' => 'homepage', 'value' => 'Explore deals', 'type' => 'string', 'is_public' => true],
            ['key' => 'home.banner_link', 'group' => 'homepage', 'value' => '/search?category=hotels', 'type' => 'string', 'is_public' => true],
            ['key' => 'home.banner_image', 'group' => 'homepage', 'value' => '', 'type' => 'string', 'is_public' => true],

            // AI travel assistant (controlled from the admin panel)
            ['key' => 'ai.enabled', 'group' => 'ai', 'value' => '1', 'type' => 'bool', 'is_public' => true],
            ['key' => 'ai.assistant_name', 'group' => 'ai', 'value' => 'TripCash AI', 'type' => 'string', 'is_public' => true],
            ['key' => 'ai.welcome_message', 'group' => 'ai', 'value' => "Hi! I'm your TripCash travel assistant 🧭 Ask me to find cheap flights, the best cashback hotels, or plan a full itinerary.", 'type' => 'string', 'is_public' => true],
            ['key' => 'ai.system_prompt', 'group' => 'ai', 'value' => '', 'type' => 'string', 'is_public' => false],
            ['key' => 'ai.suggestions', 'group' => 'ai', 'value' => "Best hotel in Goa under ₹5000\nCheapest flight from Delhi to Dubai\nBest Thailand family package\nPlan a 3-day North Goa itinerary", 'type' => 'string', 'is_public' => true],
            ['key' => 'ai.provider_priority', 'group' => 'ai', 'value' => 'groq,gemini,openai', 'type' => 'string', 'is_public' => false],
            ['key' => 'ai.groq_key', 'group' => 'ai', 'value' => '', 'type' => 'string', 'is_public' => false],
            ['key' => 'ai.gemini_key', 'group' => 'ai', 'value' => '', 'type' => 'string', 'is_public' => false],
            ['key' => 'ai.openai_key', 'group' => 'ai', 'value' => '', 'type' => 'string', 'is_public' => false],
        ];

        foreach ($settings as $s) {
            Setting::updateOrCreate(['key' => $s['key']], $s);
        }
    }
}
