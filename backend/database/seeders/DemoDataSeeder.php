<?php

namespace Database\Seeders;

use App\Models\AffiliateNetwork;
use App\Models\Booking;
use App\Models\Offer;
use App\Models\Provider;
use App\Models\Referral;
use App\Models\Role;
use App\Models\SavedItem;
use App\Models\SupportTicket;
use App\Models\User;
use App\Models\Withdrawal;
use App\Services\Cashback\CashbackService;
use App\Services\Wallet\WalletService;
use Closure;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Populates every section with realistic demo data for easy testing.
 * Each block is isolated: if one fails it is logged and skipped — the seed never
 * aborts, so admin + the rest of the data always survive.
 */
class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $providers = Provider::all();
        if ($providers->isEmpty()) {
            $this->command?->warn('No providers — run ProviderSeeder first.');

            return;
        }
        $categories = array_keys(config('tripcash.categories'));

        $this->safe('offers', fn () => $this->seedOffers($providers));
        $this->safe('staff', fn () => $this->seedStaff());

        $demo = null;
        $users = collect();
        $allUsers = collect();
        $this->safe('users', function () use (&$demo, &$users, &$allUsers) {
            $demo = User::where('email', 'user@tripcash.test')->first()
                ?? User::factory()->create(['name' => 'Demo Traveller', 'email' => 'user@tripcash.test']);
            $demo->update([
                'phone' => '+919876500000', 'kyc_status' => 'approved', 'kyc_full_name' => 'Demo Traveller',
                'kyc_pan' => 'ABCDE1234F', 'kyc_payout_method' => 'upi', 'kyc_payout_details' => ['upi' => 'demo@upi'],
                'kyc_submitted_at' => now()->subDays(5), 'kyc_reviewed_at' => now()->subDays(4),
            ]);
            $demo->roles()->syncWithoutDetaching([Role::where('name', 'user')->value('id')]);

            $users = User::factory()->count(14)->create();
            $users->take(2)->each(fn ($u) => $u->update([
                'kyc_status' => 'pending', 'kyc_full_name' => $u->name, 'kyc_pan' => 'PANXX'.rand(1000, 9999).'Z',
                'kyc_payout_method' => 'bank',
                'kyc_payout_details' => ['account' => '00011122233', 'ifsc' => 'HDFC0001234', 'name' => $u->name],
                'kyc_submitted_at' => now()->subDays(rand(1, 3)),
            ]));
            $users->each(fn ($u) => $u->roles()->syncWithoutDetaching([Role::where('name', 'user')->value('id')]));
            $allUsers = $users->push($demo);
        });

        if ($demo) {
            $this->safe('bookings+cashbacks', fn () => $this->seedBookings($allUsers, $demo, $providers));
            $this->safe('withdrawals', fn () => $this->seedWithdrawals($demo, $users));
            $this->safe('referrals', fn () => $this->seedReferrals($demo, $users));
            $this->safe('saved items', fn () => $this->seedSaved($demo));
            $this->safe('support tickets', fn () => $this->seedSupport($allUsers));
            $this->safe('notifications', fn () => $this->seedNotifications($allUsers));
        }

        $this->safe('affiliate networks', fn () => $this->seedNetworks());
        $this->safe('search logs', fn () => $this->seedSearchLogs($categories));
        $this->safe('audit logs', fn () => $this->seedAuditLogs());

        $this->command?->info('Demo data seeding finished.');
    }

    private function safe(string $label, Closure $fn): void
    {
        try {
            $fn();
            $this->command?->line("  <info>✓</info> {$label}");
        } catch (\Throwable $e) {
            $this->command?->warn("  ✗ {$label}: ".$e->getMessage());
        }
    }

    private function seedBookings($allUsers, User $demo, $providers): void
    {
        $cashback = app(CashbackService::class);
        $wallet = app(WalletService::class);

        foreach ($allUsers as $user) {
            $count = $user->is($demo) ? 8 : rand(1, 4);
            for ($i = 0; $i < $count; $i++) {
                $provider = $providers->random();
                $cats = $provider->categories ?: ['hotels'];
                $category = $cats[array_rand($cats)];
                $amount = rand(1500, 60000);

                $booking = Booking::create([
                    'user_id' => $user->id, 'provider_id' => $provider->id, 'category' => $category,
                    'title' => ucfirst($category).' booking · '.$provider->name,
                    'amount' => $amount,
                    'commission_amount' => round($amount * (float) $provider->commission_percent / 100, 2),
                    'currency' => 'INR', 'status' => 'confirmed',
                    'external_ref' => 'EXT-'.strtoupper(Str::random(8)), 'booked_at' => now()->subDays(rand(0, 60)),
                ]);

                $cb = $cashback->createForBooking($booking);
                if (! $cb) {
                    continue;
                }
                $outcome = ['pending', 'confirmed', 'withdrawable', 'withdrawable', 'rejected'][rand(0, 4)];
                if ($outcome === 'confirmed') {
                    $cashback->confirm($cb);
                } elseif ($outcome === 'withdrawable') {
                    $cashback->confirm($cb);
                    $cashback->mature($cb);
                } elseif ($outcome === 'rejected') {
                    $cashback->reject($cb, 'Booking cancelled by user');
                }
            }
            $wallet->credit($user, rand(50, 300), 'referral_credit', description: 'Welcome bonus', idempotencyKey: 'demo_bonus_'.$user->id);
        }
    }

    private function seedWithdrawals(User $demo, $users): void
    {
        $wallet = app(WalletService::class);

        if ((float) $wallet->walletFor($demo->fresh())->balance >= 500) {
            $wallet->debit($demo, 500, 'withdrawal_debit', description: 'Withdrawal request');
            Withdrawal::create(['user_id' => $demo->id, 'amount' => 500, 'currency' => 'INR', 'method' => 'upi', 'payout_details' => ['upi' => 'demo@upi'], 'status' => 'requested']);
        }
        foreach ($users->take(3) as $u) {
            $bal = (float) $wallet->walletFor($u->fresh())->balance;
            if ($bal >= 250) {
                $wallet->debit($u, 250, 'withdrawal_debit', description: 'Withdrawal request');
                Withdrawal::create([
                    'user_id' => $u->id, 'amount' => 250, 'currency' => 'INR', 'method' => 'bank',
                    'payout_details' => ['account' => '123456789', 'ifsc' => 'ICIC0000123', 'name' => $u->name],
                    'status' => ['requested', 'processing', 'paid'][rand(0, 2)],
                ]);
            }
        }
    }

    private function seedReferrals(User $demo, $users): void
    {
        foreach ($users->take(4) as $ref) {
            Referral::create([
                'referrer_id' => $demo->id, 'referee_id' => $ref->id, 'code' => $demo->referral_code,
                'status' => ['pending', 'qualified', 'rewarded'][rand(0, 2)], 'reward_amount' => 100,
                'ip_address' => '103.0.0.'.rand(1, 254),
            ]);
        }
    }

    private function seedSaved(User $demo): void
    {
        SavedItem::create(['user_id' => $demo->id, 'kind' => 'saved_hotel', 'category' => 'hotels', 'payload' => ['title' => 'Grand Plaza Goa', 'price' => 5400]]);
        SavedItem::create(['user_id' => $demo->id, 'kind' => 'watchlist', 'category' => 'flights', 'payload' => ['title' => 'DEL → DXB'], 'target_price' => 18000]);
    }

    private function seedSupport($allUsers): void
    {
        foreach ($allUsers->take(5) as $u) {
            $ticket = SupportTicket::create([
                'user_id' => $u->id,
                'subject' => ['Cashback not credited', 'Withdrawal delay', 'KYC question', 'Booking issue'][rand(0, 3)],
                'category' => 'general', 'priority' => ['normal', 'high', 'urgent'][rand(0, 2)],
                'status' => ['open', 'pending', 'resolved'][rand(0, 2)], 'last_reply_at' => now()->subHours(rand(1, 72)),
            ]);
            $ticket->messages()->create(['user_id' => $u->id, 'is_staff' => false, 'body' => 'Hi, I need help with my recent transaction.']);
        }
    }

    private function seedOffers($providers): void
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
        $manager = User::updateOrCreate(['email' => 'manager@tripcash.test'], [
            'name' => 'Ops Manager', 'email_verified_at' => now(), 'status' => 'active',
        ]);
        $manager->roles()->syncWithoutDetaching([Role::where('name', 'manager')->value('id')]);

        $support = User::updateOrCreate(['email' => 'support@tripcash.test'], [
            'name' => 'Support Agent', 'email_verified_at' => now(), 'status' => 'active',
        ]);
        $support->roles()->syncWithoutDetaching([Role::where('name', 'support')->value('id')]);

        // Exact-bcrypt passwords at the DB level so staff logins always work.
        DB::table('users')->whereIn('email', ['manager@tripcash.test', 'support@tripcash.test'])
            ->update(['password' => bcrypt('password')]);
    }

    private function seedNotifications($users): void
    {
        $payload = json_encode(['title' => 'Welcome to TripCash 🎉', 'message' => 'Start searching and earn cashback on every booking!', 'url' => '/dashboard']);
        $now = now();
        $rows = [];
        foreach ($users as $u) {
            $rows[] = [
                'id' => (string) Str::uuid(), 'type' => 'admin.broadcast', 'notifiable_type' => User::class,
                'notifiable_id' => $u->id, 'data' => $payload, 'category' => 'promo', 'read_at' => null,
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
                    'travellers' => rand(1, 4), 'result_count' => rand(4, 40), 'response_ms' => rand(20, 480),
                    'cache_hit' => (bool) rand(0, 1), 'ip_address' => '103.0.0.'.rand(1, 254),
                    'created_at' => $date->copy()->setTime(rand(0, 23), rand(0, 59)),
                ];
            }
        }
        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('search_logs')->insert($chunk);
        }
    }

    private function seedNetworks(): void
    {
        foreach ([['Admitad', false], ['Cuelinks', true], ['vCommission', true]] as [$name, $active]) {
            AffiliateNetwork::updateOrCreate(['slug' => Str::slug($name)], ['name' => $name, 'postback_secret' => Str::random(40), 'is_active' => $active]);
        }
    }

    private function seedAuditLogs(): void
    {
        $admin = User::where('email', 'admin@tripcash.test')->first();
        $actions = [
            'admin.login.success', 'PUT admin/providers/1', 'PUT admin/withdrawals/1/approve',
            'PUT admin/kyc/3/approve', 'POST admin/offers', 'PUT admin/cashbacks/2/confirm',
            'POST admin/notifications', 'PUT admin/settings', 'PUT admin/integrations',
        ];
        $rows = [];
        foreach ($actions as $i => $action) {
            $rows[] = [
                'user_id' => $admin?->id, 'action' => $action, 'new_values' => json_encode(['demo' => true]),
                'ip_address' => '103.0.0.'.rand(1, 254), 'user_agent' => 'Mozilla/5.0 (Admin Demo)',
                'created_at' => now()->subHours($i * 3),
            ];
        }
        DB::table('audit_logs')->insert($rows);
    }
}
