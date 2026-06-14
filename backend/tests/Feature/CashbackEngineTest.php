<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Cashback;
use App\Models\CashbackRule;
use App\Models\Provider;
use App\Models\User;
use App\Services\Cashback\CashbackService;
use App\Services\Wallet\WalletService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CashbackEngineTest extends TestCase
{
    use RefreshDatabase;

    private function makeProvider(): Provider
    {
        return Provider::create([
            'name' => 'Booking.com', 'slug' => 'booking-com', 'adapter' => 'generic_rest',
            'categories' => ['hotels'], 'is_active' => true, 'priority' => 10,
            'commission_percent' => 10,
        ]);
    }

    public function test_pending_cashback_is_created_and_parked_in_pending_balance(): void
    {
        $provider = $this->makeProvider();
        CashbackRule::create([
            'name' => 'Global', 'type' => 'percentage', 'value' => 40, 'priority' => 1000, 'is_active' => true,
        ]);
        $user = User::factory()->create();

        $booking = Booking::create([
            'user_id' => $user->id, 'provider_id' => $provider->id, 'category' => 'hotels',
            'amount' => 5000, 'currency' => 'INR', 'status' => 'confirmed',
        ]);

        $cashback = app(CashbackService::class)->createForBooking($booking);

        // commission = 5000 * 10% = 500; cashback = 500 * 40% = 200
        $this->assertNotNull($cashback);
        $this->assertEquals(200.00, (float) $cashback->amount);
        $this->assertSame(Cashback::PENDING, $cashback->status);
        $this->assertEquals(200.00, (float) $user->fresh()->wallet->pending_balance);
        $this->assertEquals(0.00, (float) $user->fresh()->wallet->balance);
    }

    public function test_full_lifecycle_pending_to_withdrawable(): void
    {
        $provider = $this->makeProvider();
        CashbackRule::create(['name' => 'Global', 'type' => 'percentage', 'value' => 40, 'priority' => 1000, 'is_active' => true]);
        $user = User::factory()->create();
        $booking = Booking::create([
            'user_id' => $user->id, 'provider_id' => $provider->id, 'category' => 'hotels',
            'amount' => 5000, 'currency' => 'INR', 'status' => 'confirmed',
        ]);

        $service = app(CashbackService::class);
        $cashback = $service->createForBooking($booking);
        $service->confirm($cashback);

        $cashback->refresh();
        $this->assertSame(Cashback::CONFIRMED, $cashback->status);
        $this->assertNotNull($cashback->matures_at);

        $service->mature($cashback);

        $wallet = $user->fresh()->wallet;
        $this->assertSame(Cashback::WITHDRAWABLE, $cashback->fresh()->status);
        $this->assertEquals(200.00, (float) $wallet->balance);
        $this->assertEquals(0.00, (float) $wallet->pending_balance);
    }

    public function test_provider_category_rule_beats_global(): void
    {
        $provider = $this->makeProvider();
        CashbackRule::create(['name' => 'Global', 'type' => 'percentage', 'value' => 40, 'priority' => 1000, 'is_active' => true]);
        CashbackRule::create(['name' => 'Flat', 'provider_id' => $provider->id, 'category' => 'hotels', 'type' => 'fixed', 'value' => 750, 'priority' => 100, 'is_active' => true]);
        $user = User::factory()->create();
        $booking = Booking::create([
            'user_id' => $user->id, 'provider_id' => $provider->id, 'category' => 'hotels',
            'amount' => 5000, 'currency' => 'INR', 'status' => 'confirmed',
        ]);

        $cashback = app(CashbackService::class)->createForBooking($booking);

        $this->assertEquals(750.00, (float) $cashback->amount); // fixed rule wins
    }

    public function test_rejecting_reverses_pending_balance(): void
    {
        $provider = $this->makeProvider();
        CashbackRule::create(['name' => 'Global', 'type' => 'percentage', 'value' => 40, 'priority' => 1000, 'is_active' => true]);
        $user = User::factory()->create();
        $booking = Booking::create([
            'user_id' => $user->id, 'provider_id' => $provider->id, 'category' => 'hotels',
            'amount' => 5000, 'currency' => 'INR', 'status' => 'confirmed',
        ]);

        $service = app(CashbackService::class);
        $cashback = $service->createForBooking($booking);
        $service->reject($cashback, 'cancelled');

        $this->assertSame(Cashback::REJECTED, $cashback->fresh()->status);
        $this->assertEquals(0.00, (float) $user->fresh()->wallet->pending_balance);
    }

    public function test_wallet_credit_is_idempotent(): void
    {
        $user = User::factory()->create();
        $wallet = app(WalletService::class);

        $wallet->credit($user, 100, 'cashback_credit', idempotencyKey: 'same-key');
        $wallet->credit($user, 100, 'cashback_credit', idempotencyKey: 'same-key');

        $this->assertEquals(100.00, (float) $user->fresh()->wallet->balance);
        $this->assertSame(1, $user->walletTransactions()->count());
    }
}
