<?php

namespace Tests\Unit;

use App\Services\Affiliate\AffiliateTracker;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * HMAC postback verification is pure logic; we instantiate the tracker without
 * its constructor dependencies to test the verification in isolation.
 */
class AffiliateSignatureTest extends TestCase
{
    private function tracker(): AffiliateTracker
    {
        return (new ReflectionClass(AffiliateTracker::class))->newInstanceWithoutConstructor();
    }

    private function sign(array $payload, string $secret): string
    {
        ksort($payload);

        return hash_hmac('sha256', http_build_query($payload), $secret);
    }

    public function test_valid_signature_is_accepted(): void
    {
        $secret = 'super-secret';
        $payload = ['provider' => 'booking-com', 'booking_ref' => 'BK1', 'amount' => 5400, 'status' => 'confirmed'];

        $this->assertTrue($this->tracker()->verifySignature($secret, $payload, $this->sign($payload, $secret)));
    }

    public function test_tampered_amount_is_rejected(): void
    {
        $secret = 'super-secret';
        $payload = ['provider' => 'booking-com', 'booking_ref' => 'BK1', 'amount' => 5400, 'status' => 'confirmed'];
        $sig = $this->sign($payload, $secret);

        $payload['amount'] = 999999;
        $this->assertFalse($this->tracker()->verifySignature($secret, $payload, $sig));
    }

    public function test_wrong_secret_is_rejected(): void
    {
        $payload = ['provider' => 'booking-com', 'booking_ref' => 'BK1', 'amount' => 5400];
        $sig = $this->sign($payload, 'right-secret');

        $this->assertFalse($this->tracker()->verifySignature('wrong-secret', $payload, $sig));
    }
}
