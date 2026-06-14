<?php

namespace Tests\Feature;

use App\Models\AffiliateNetwork;
use App\Models\BookingClick;
use App\Models\CashbackRule;
use App\Models\Provider;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class PostbackTest extends TestCase
{
    use RefreshDatabase;

    private function sign(array $payload, string $secret): string
    {
        unset($payload['sig'], $payload['signature']);
        ksort($payload);

        return hash_hmac('sha256', http_build_query($payload), $secret);
    }

    public function test_signed_postback_creates_booking_and_cashback(): void
    {
        $secret = 'network-secret';
        $network = AffiliateNetwork::create(['name' => 'Impact', 'slug' => 'impact', 'postback_secret' => $secret, 'is_active' => true]);
        $provider = Provider::create([
            'name' => 'Booking.com', 'slug' => 'booking-com', 'adapter' => 'generic_rest',
            'categories' => ['hotels'], 'is_active' => true, 'priority' => 10, 'commission_percent' => 10,
            'affiliate_network_id' => $network->id,
        ]);
        CashbackRule::create(['name' => 'Global', 'type' => 'percentage', 'value' => 40, 'priority' => 1000, 'is_active' => true]);

        $user = User::factory()->create();
        $clickId = (string) Str::uuid();
        BookingClick::create([
            'click_id' => $clickId, 'user_id' => $user->id, 'provider_id' => $provider->id,
            'category' => 'hotels', 'status' => 'clicked',
        ]);

        $payload = [
            'provider' => 'booking-com',
            'click_id' => $clickId,
            'booking_ref' => 'BK-1001',
            'amount' => 5000,
            'status' => 'confirmed',
            'category' => 'hotels',
        ];
        $sig = $this->sign($payload, $secret);

        $response = $this->withHeaders(['X-Signature' => $sig])
            ->postJson("/api/v1/postback/impact", $payload);

        $response->assertOk()->assertJson(['ok' => true]);

        $this->assertDatabaseHas('bookings', [
            'provider_id' => $provider->id, 'external_ref' => 'BK-1001', 'user_id' => $user->id,
        ]);
        // commission 500, cashback 200, confirmed by the postback status
        $this->assertDatabaseHas('cashbacks', [
            'user_id' => $user->id, 'amount' => 200.00, 'status' => 'confirmed',
        ]);
    }

    public function test_invalid_signature_is_rejected(): void
    {
        $network = AffiliateNetwork::create(['name' => 'Impact', 'slug' => 'impact', 'postback_secret' => 'secret', 'is_active' => true]);
        Provider::create([
            'name' => 'Booking.com', 'slug' => 'booking-com', 'adapter' => 'generic_rest',
            'categories' => ['hotels'], 'is_active' => true, 'priority' => 10, 'commission_percent' => 10,
            'affiliate_network_id' => $network->id,
        ]);

        $response = $this->withHeaders(['X-Signature' => 'deadbeef'])
            ->postJson('/api/v1/postback/impact', ['provider' => 'booking-com', 'booking_ref' => 'X', 'amount' => 1]);

        $response->assertStatus(401);
    }
}
