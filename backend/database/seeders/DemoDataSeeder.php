<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Cashback;
use App\Models\Offer;
use App\Models\Provider;
use App\Models\Role;
use App\Models\SavedItem;
use App\Models\SearchLog;
use App\Models\SupportTicket;
use App\Models\User;
use App\Models\Withdrawal;
use App\Services\Cashback\CashbackService;
use App\Services\Wallet\WalletService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Populates every section of the platform with realistic demo data so the full
 * app (user dashboard + admin control center) can be tested end-to-end.
 */
class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $cashbackService = app(CashbackService::class);
        $wallet = app(WalletService::class);
        $providers = Provider::all();
        if ($providers->isEmpty()) {
            $this->command?->warn('No providers — run ProviderSeeder first.');

            return;
        }

        $categories = array_keys(config('travelcash.categories'));

        // --- Offers / Deals catalog ---
        $this->seedOffers($providers, $categories);

        // --- Staff accounts ---
        $this->seedStaff();

        // --- Demo + extra users ---
        $demo = User::where('email', 'user@travelcash.test')->first()
            ?? User::factory()->create(['name' => 'Demo Traveller', 'email' => 'user@travelcash.test']);
        $demo->update([
            'phone' => '+919876500000',
            'kyc_status' => 'approved',
            'kyc_full_name' => 'Demo Traveller',
            'kyc_pan' => 'ABCDE1234F',
            'kyc_payout_method' => 'upi',
            'kyc_payout_details' => ['upi' => 'demo@upi'],
            'kyc_submitted_at' => now()->subDays(5),
            'kyc_reviewed_at' => now()->subDays(4),
        ]);
        if (! $demo->hasRole('user')) {
            $demo->roles()->syncWithoutDetaching([Role::where('name', 'user')->value('id')]);
        }

        $users = User::factory()->count(14)->create();
        // A couple of pending KYC users for the admin to review
        $users->take(2)->each(fn ($u) => $u->update([
            'kyc_status' => 'pending', 'kyc_full_name' => $u->name, 'kyc_pan' => 'PANXX'.rand(1000, 9999).'Z',
            'kyc_payout_method' => 'bank', 'kyc_payout_details' => ['account' => '00011122233', 'ifsc' => 'HDFC0001234', 'name' => $u->name],
            'kyc_submitted_at' => now()->subDays(rand(1, 3)),
        ]));
        $users->each(fn ($u) => $u->roles()->syncWithoutDetaching([Role::where('name', 'user')->value('id')]));

        $allUsers = $users->push($demo);

        // --- Bookings + cashbacks (every status) + wallet movements ---
        foreach ($allUsers as $user) {
            $bookingCount = $user->is($demo) ? 8 : rand(1, 4);
            for ($i = 0; $i < $bookingCount; $i++) {
                $provider = $providers->random();
                $category = collect($provider->categories)->random();
                $amount = rand(1500, 60000);

                $booking = Booking::create([
                    'user_id' => $user->id,
                    'provider_id' => $provider->id,
                    'category' => $category,
                    'title' => ucfirst($category).' booking · '.$provider->name,
                    'amount' => $amount,
                    'commission_amount' => round($amount * (float) $provider->commission_percent / 100, 2),
                    'currency' => 'INR',
                    'status' => 'confirmed',
                    'external_ref' => 'EXT-'.strtoupper(Str::random(8)),
                    'booked_at' => now()->subDays(rand(0, 60)),
                ]);

                $cashback = $cashbackService->createForBooking($booking);
                if (! $cashback) {
                    continue;
                }

                // Spread across the lifecycle for testing.
                $outcome = ['pending', 'confirmed', 'withdrawable', 'withdrawable', 'rejected'][rand(0, 4)];
                if ($outcome === 'confirmed') {
                    $cashbackService->confirm($cashback);
                } elseif ($outcome === 'withdrawable') {
                    $cashbackService->confirm($cashback);
                    $cashbackService->mature($cashback);
                } elseif ($outcome === 'rejected') {
                    $cashbackService->reject($cashback, 'Booking cancelled by user');
                }
            }

            // Referral reward + signup bonus so wallets have varied balances
            $wallet->credit($user, rand(50, 300), 'referral_credit', description: 'Welcome bonus', idempotencyKey: 'demo_bonus_'.$user->id);
        }

        // --- Withdrawal request for the demo user (has withdrawable balance) ---
        $demoWallet = $wallet->walletFor($demo->fresh());
        if ((float) $demoWallet->balance >= 500) {
            $wallet->debit($demo, 500, 'withdrawal_debit', description: 'Withdrawal request');
            Withdrawal::create([
                'user_id' => $demo->id, 'amount' => 500, 'currency' => 'INR', 'method' => 'upi',
                'payout_details' => ['upi' => 'demo@upi'], 'status' => 'requested',
            ]);
        }
        // A couple more withdrawals from random users (in different states)
        foreach ($users->take(3) as $u) {
            $w = $wallet->walletFor($u->fresh());
            if ((float) $w->balance >= 250) {
                $amt = min(250, (float) $w->balance);
                $wallet->debit($u, $amt, 'withdrawal_debit', description: 'Withdrawal request');
                Withdrawal::create([
                    'user_id' => $u->id, 'amount' => $amt, 'currency' => 'INR', 'method' => 'bank',
                    'payout_details' => ['account' => '123456789', 'ifsc' => 'ICIC0000123', 'name' => $u->name],
                    'status' => ['requested', 'processing', 'paid'][rand(0, 2)],
                ]);
            }
        }

        // --- Referrals for demo user ---
        foreach ($users->take(4) as $i => $ref) {
            \App\Models\Referral::create([
                'referrer_id' => $demo->id, 'referee_id' => $ref->id, 'code' => $demo->referral_code,
                'status' => ['pending', 'qualified', 'rewarded'][rand(0, 2)],
                'reward_amount' => 100, 'ip_address' => '103.0.0.'.rand(1, 254),
            ]);
        }

        // --- Saved items / watchlist for demo user ---
        SavedItem::create(['user_id' => $demo->id, 'kind' => 'saved_hotel', 'category' => 'hotels', 'payload' => ['title' => 'Grand Plaza Goa', 'price' => 5400]]);
        SavedItem::create(['user_id' => $demo->id, 'kind' => 'watchlist', 'category' => 'flights', 'payload' => ['title' => 'DEL → DXB'], 'target_price' => 18000]);

        // --- Support tickets ---
        foreach ($allUsers->take(5) as $u) {
            $ticket = SupportTicket::create([
                'user_id' => $u->id, 'subject' => ['Cashback not credited', 'Withdrawal delay', 'KYC question', 'Booking issue'][rand(0, 3)],
                'category' => 'general', 'priority' => ['normal', 'high', 'urgent'][rand(0, 2)],
                'status' => ['open', 'pending', 'resolved'][rand(0, 2)], 'last_reply_at' => now()->subHours(rand(1, 72)),
            ]);
            $ticket->messages()->create(['user_id' => $u->id, 'is_staff' => false, 'body' => 'Hi, I need help with my recent transaction.']);
        }

        // --- Admin broadcast notifications for all users ---
        $this->seedNotifications($allUsers);

        // --- Search logs (last 14 days) for analytics charts ---
        $this->seedSearchLogs($categories);

        $this->command?->info('Demo data seeded across all sections.');
    }

    private function seedOffers($providers, array $categories): void
    {
        $deals = [
            ['Up to 60% cashback on hotels', 'hotels', 'percentage', 60, true],
            ['Flat ₹500 back on flights', 'flights', 'flat', 500, true],
            ['Thailand packages — 50% cashback', 'packages', 'percentage', 50, true],
            ['Cabs: ₹50 back every ride', 'cabs', 'flat', 50, false],
            ['Train bookings 30% cashback', 'trains', 'percentage', 30, false],
            ['Airport transfers ₹100 back', 'transfers', 'flat', 100, false],
            ['Tourist guides 40% cashback', 'guides', 'percentage', 40, false],
            ['Activities — up to ₹300 back', 'activities', 'flat', 300, true],
        ];
        foreach ($deals as $i => [$title, $cat, $type, $val, $featured]) {
            Offer::updateOrCreate(['slug' => Str::slug($title)], [
                'provider_id' => $providers->first(fn ($p) => in_array($cat, $p->categories ?? []))?->id ?? $providers->random()->id,
                'title' => $title, 'category' => $cat, 'cashback_label' => $title,
                'cashback_type' => $type, 'cashback_value' => $val,
                'description' => 'Limited-period cashback offer on '.$cat.'.',
                'terms' => 'Cashback confirmed after the provider validates the booking. T&C apply.',
                'image_url' => "https://picsum.photos/seed/deal{$i}/640/360",
                'is_featured' => $featured, 'is_active' => true, 'sort_order' => $i,
            ]);
        }
    }

    private function seedStaff(): void
    {
        $manager = User::updateOrCreate(['email' => 'manager@travelcash.test'], [
            'name' => 'Ops Manager', 'password' => 'password', 'email_verified_at' => now(), 'status' => 'active',
        ]);
        $manager->roles()->syncWithoutDetaching([Role::where('name', 'manager')->value('id')]);

        $support = User::updateOrCreate(['email' => 'support@travelcash.test'], [
            'name' => 'Support Agent', 'password' => 'password', 'email_verified_at' => now(), 'status' => 'active',
        ]);
        $support->roles()->syncWithoutDetaching([Role::where('name', 'support')->value('id')]);
    }

    private function seedNotifications($users): void
    {
        $payload = json_encode(['title' => 'Welcome to TravelCash 🎉', 'message' => 'Start searching and earn cashback on every booking!', 'url' => '/dashboard']);
        $now = now();
        $rows = [];
        foreach ($users as $u) {
            $rows[] = [
                'id' => (string) Str::uuid(), 'type' => 'admin.broadcast',
                'notifiable_type' => User::class, 'notifiable_id' => $u->id,
                'data' => $payload, 'category' => 'promo', 'read_at' => null,
                'created_at' => $now, 'updated_at' => $now,
            ];
        }
        if ($rows) {
            DB::table('notifications')->insert($rows);
        }
    }

    private function seedSearchLogs(array $categories): void
    {
        $rows = [];
        for ($d = 0; $d < 14; $d++) {
            $date = now()->subDays($d);
            foreach (range(1, rand(20, 120)) as $n) {
                $rows[] = [
                    'category' => $categories[array_rand($categories)],
                    'destination' => ['Goa', 'Dubai', 'Bali', 'Manali', 'Jaipur'][rand(0, 4)],
                    'travellers' => rand(1, 4), 'result_count' => rand(4, 40),
                    'response_ms' => rand(20, 480), 'cache_hit' => (bool) rand(0, 1),
                    'ip_address' => '103.0.0.'.rand(1, 254),
                    'created_at' => $date->copy()->setTime(rand(0, 23), rand(0, 59)),
                ];
            }
        }
        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('search_logs')->insert($chunk);
        }
    }
}
