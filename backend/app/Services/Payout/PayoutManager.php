<?php

namespace App\Services\Payout;

use App\Models\Withdrawal;

/**
 * Resolves payout gateways and processes a withdrawal through the chosen one.
 * The wallet was already debited when the user requested the withdrawal, so here
 * we only move it from "requested/approved" into the gateway and record the result.
 */
class PayoutManager
{
    /** @var array<string, class-string<PayoutGateway>> */
    private array $registry = [
        'manual' => ManualGateway::class,
        'razorpay' => RazorpayGateway::class,
        'paypal' => PayPalGateway::class,
    ];

    public function gateway(string $key): PayoutGateway
    {
        $class = $this->registry[$key] ?? ManualGateway::class;

        return app($class);
    }

    /** Gateways that are configured + ready to use (for the admin dropdown). */
    public function availableGateways(): array
    {
        $out = [];
        foreach ($this->registry as $key => $class) {
            /** @var PayoutGateway $g */
            $g = app($class);
            $out[$key] = $g->isConfigured();
        }

        return $out;
    }

    /** Process a withdrawal payout. Returns the gateway result. */
    public function process(Withdrawal $withdrawal, string $gatewayKey): array
    {
        $gateway = $this->gateway($gatewayKey);

        if (! $gateway->isConfigured()) {
            return ['ok' => false, 'status' => 'failed', 'reference' => null, 'raw' => ['error' => "Gateway {$gatewayKey} is not configured."]];
        }

        $result = $gateway->payout($withdrawal);

        // On failure: record gateway response but leave the wallet/refund decision to
        // the caller (which holds the WalletService). On success: advance the status.
        if (! $result['ok']) {
            $withdrawal->update([
                'gateway' => $gateway->key(),
                'gateway_response' => $result['raw'],
            ]);

            return $result;
        }

        $withdrawal->update([
            'gateway' => $gateway->key(),
            'status' => $result['status'] === 'processed' ? Withdrawal::PAID : Withdrawal::PROCESSING,
            'gateway_payout_id' => $result['reference'],
            'gateway_response' => $result['raw'],
            'reference' => $result['reference'] ?? $withdrawal->reference,
        ]);

        return $result;
    }
}
