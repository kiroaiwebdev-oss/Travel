<?php

namespace App\Services\Affiliate;

use App\DTO\NormalizedOffer;
use App\Models\Booking;
use App\Models\BookingClick;
use App\Models\Provider;
use App\Services\Cashback\CashbackService;
use App\Services\Providers\ProviderManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * The affiliate funnel:
 *   1. registerClick()  — user clicks an offer; we mint a click_id, persist it,
 *      drop a tracking cookie, and build the provider deep-link (with our sub-id).
 *   2. handlePostback() — provider/network calls back server-to-server when a
 *      booking happens. We HMAC-verify, match the click, create a Booking and a
 *      PENDING cashback.
 *   3. confirm/reject  — later postbacks settle the commission.
 */
class AffiliateTracker
{
    public function __construct(
        private readonly ProviderManager $providers,
        private readonly CashbackService $cashback,
    ) {}

    /**
     * Register a click-out and return the destination URL to redirect to.
     *
     * @return array{click: BookingClick, redirect_url: string}
     */
    public function registerClick(Provider $provider, array $params, Request $request): array
    {
        $clickId = (string) Str::uuid();

        $click = BookingClick::create([
            'click_id' => $clickId,
            'user_id' => $request->user()?->id,
            'provider_id' => $provider->id,
            'category' => $params['category'] ?? null,
            'offer_ref' => $params['offer_ref'] ?? null,
            'expected_amount' => $params['amount'] ?? null,
            'currency' => $params['currency'] ?? config('tripcash.currency'),
            'session_id' => $request->session()->getId(),
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'landing_url' => $params['url'] ?? null,
            'status' => 'clicked',
        ]);

        // Build the provider deep-link carrying our click id as the sub-id.
        $offer = new NormalizedOffer(
            providerId: $provider->id,
            providerSlug: $provider->slug,
            providerName: $provider->name,
            category: (string) ($params['category'] ?? ''),
            title: '',
            price: (float) ($params['amount'] ?? 0),
            offerRef: $params['offer_ref'] ?? null,
            bookUrl: $params['url'] ?? null,
        );

        $redirect = $this->providers->adapterFor($provider)->buildBookUrl($offer, $clickId);

        return ['click' => $click, 'redirect_url' => $redirect];
    }

    /** Verify the HMAC signature on a server-to-server postback. */
    public function verifySignature(string $secret, array $payload, string $signature): bool
    {
        // Sign the canonical, sorted payload (excluding the signature itself).
        unset($payload['sig'], $payload['signature']);
        ksort($payload);
        $expected = hash_hmac('sha256', http_build_query($payload), $secret);

        return hash_equals($expected, $signature);
    }

    /**
     * Process a conversion postback. Idempotent on (provider, external_ref).
     *
     * @return array{ok: bool, booking?: Booking, reason?: string}
     */
    public function handlePostback(Provider $provider, array $payload): array
    {
        $clickId = $payload['click_id'] ?? $payload['subid'] ?? null;
        $externalRef = $payload['booking_ref'] ?? $payload['order_id'] ?? null;
        $amount = (float) ($payload['amount'] ?? $payload['sale_amount'] ?? 0);
        $status = strtolower((string) ($payload['status'] ?? 'confirmed'));

        if (! $clickId || ! $externalRef) {
            return ['ok' => false, 'reason' => 'missing click_id or booking_ref'];
        }

        $click = BookingClick::where('click_id', $clickId)->first();

        // Idempotency: same external_ref for the same provider -> no duplicate booking.
        $booking = Booking::firstOrNew([
            'provider_id' => $provider->id,
            'external_ref' => $externalRef,
        ]);

        $isNew = ! $booking->exists;

        $booking->fill([
            'user_id' => $click?->user_id,
            'booking_click_id' => $click?->id,
            'category' => $payload['category'] ?? $click?->category ?? 'hotels',
            'title' => $payload['title'] ?? null,
            'details' => $payload['details'] ?? null,
            'amount' => $amount,
            'commission_amount' => (float) ($payload['commission'] ?? 0),
            'currency' => $payload['currency'] ?? $click?->currency ?? config('tripcash.currency'),
            'status' => $this->mapStatus($status),
            'booked_at' => now(),
        ])->save();

        if ($click && $click->status !== 'converted') {
            $click->update(['status' => 'converted', 'converted_at' => now()]);
        }

        // Create / settle cashback.
        $cashback = $isNew ? $this->cashback->createForBooking($booking) : $booking->cashback;

        if ($cashback) {
            match ($this->mapStatus($status)) {
                Booking::CONFIRMED, Booking::COMPLETED => $this->cashback->confirm($cashback),
                Booking::CANCELLED, Booking::REFUNDED => $this->cashback->reject($cashback, "Provider status: {$status}"),
                default => null,
            };
        }

        Log::info('Affiliate postback processed', [
            'provider' => $provider->slug,
            'external_ref' => $externalRef,
            'status' => $status,
            'new' => $isNew,
        ]);

        return ['ok' => true, 'booking' => $booking];
    }

    private function mapStatus(string $status): string
    {
        return match ($status) {
            'confirmed', 'approved', 'completed', 'paid' => Booking::CONFIRMED,
            'cancelled', 'canceled', 'declined', 'rejected' => Booking::CANCELLED,
            'refunded' => Booking::REFUNDED,
            default => Booking::PENDING,
        };
    }
}
