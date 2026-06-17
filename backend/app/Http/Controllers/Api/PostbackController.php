<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AffiliateNetwork;
use App\Models\Provider;
use App\Services\Affiliate\AffiliateTracker;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostbackController extends Controller
{
    public function __construct(private readonly AffiliateTracker $tracker) {}

    /**
     * Server-to-server conversion postback. The network signs the payload with its
     * shared secret; we verify the HMAC before recording the conversion.
     */
    public function handle(AffiliateNetwork $network, Request $request): JsonResponse
    {
        if (! $network->is_active) {
            return response()->json(['ok' => false, 'reason' => 'network inactive'], 422);
        }

        // Never accept a postback for a network without a configured secret — otherwise
        // an empty/blank secret could let an attacker forge conversions (cashback fraud).
        if (blank($network->postback_secret)) {
            return response()->json(['ok' => false, 'reason' => 'network not configured'], 422);
        }

        $payload = $request->all();
        $signature = (string) ($request->header('X-Signature') ?? $request->input('sig') ?? '');

        if (! $this->tracker->verifySignature((string) $network->postback_secret, $payload, $signature)) {
            return response()->json(['ok' => false, 'reason' => 'invalid signature'], 401);
        }

        $providerSlug = (string) ($payload['provider'] ?? '');
        $provider = Provider::where('slug', $providerSlug)
            ->where('affiliate_network_id', $network->id)
            ->first();

        if (! $provider) {
            return response()->json(['ok' => false, 'reason' => 'unknown provider'], 404);
        }

        $result = $this->tracker->handlePostback($provider, $payload);

        return response()->json($result, $result['ok'] ? 200 : 422);
    }
}
