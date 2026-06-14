<?php

namespace Tests\Unit;

use App\DTO\NormalizedOffer;
use PHPUnit\Framework\TestCase;

class NormalizedOfferTest extends TestCase
{
    private function offer(float $price = 5000.0): NormalizedOffer
    {
        return new NormalizedOffer(
            providerId: 1,
            providerSlug: 'booking-com',
            providerName: 'Booking.com',
            category: 'hotels',
            title: 'Grand Plaza',
            price: $price,
            currency: 'INR',
        );
    }

    public function test_with_cashback_and_to_array(): void
    {
        $offer = $this->offer()->withCashback(259.2);
        $arr = $offer->toArray();

        $this->assertSame(5000.0, $arr['price']);
        $this->assertSame(259.2, $arr['cashback']);
        $this->assertSame('booking-com', $arr['provider_slug']);
    }

    public function test_hash_is_stable_and_unique(): void
    {
        $this->assertSame($this->offer()->hash(), $this->offer()->hash());
        $this->assertSame(64, strlen($this->offer()->hash()));
        $this->assertNotSame($this->offer(5000.0)->hash(), $this->offer(5001.0)->hash());
    }
}
