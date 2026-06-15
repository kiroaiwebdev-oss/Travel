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
            ['key' => 'cashback.default_share_percent', 'group' => 'cashback', 'value' => '40', 'type' => 'float', 'is_public' => true],
            ['key' => 'cashback.hold_days', 'group' => 'cashback', 'value' => '30', 'type' => 'int', 'is_public' => false],
            ['key' => 'cashback.min_withdrawal', 'group' => 'cashback', 'value' => '500', 'type' => 'float', 'is_public' => true],
            ['key' => 'referral.reward_amount', 'group' => 'referral', 'value' => '100', 'type' => 'float', 'is_public' => true],
            ['key' => 'seo.meta_description', 'group' => 'seo', 'value' => 'Compare flights, hotels, trains, cabs & packages. Book through TripCash and earn real cashback.', 'type' => 'string', 'is_public' => true],
        ];

        foreach ($settings as $s) {
            Setting::updateOrCreate(['key' => $s['key']], $s);
        }
    }
}
