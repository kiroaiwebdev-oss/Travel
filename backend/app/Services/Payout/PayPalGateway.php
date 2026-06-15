<?php

namespace App\Services\Payout;

use App\Models\Withdrawal;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * PayPal Payouts. Activates when client id/secret are configured.
 * Docs: https://developer.paypal.com/docs/payouts/
 */
class PayPalGateway implements PayoutGateway
{
    public function key(): string
    {
        return 'paypal';
    }

    public function isConfigured(): bool
    {
        return ! empty(config('services.paypal.client_id')) && ! empty(config('services.paypal.secret'));
    }

    public function payout(Withdrawal $withdrawal): array
    {
        $cfg = config('services.paypal');
        $base = ($cfg['mode'] ?? 'sandbox') === 'live'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';
        $details = $withdrawal->payout_details ?? [];

        try {
            $token = Http::asForm()->withBasicAuth($cfg['client_id'], $cfg['secret'])
                ->post($base.'/v1/oauth2/token', ['grant_type' => 'client_credentials'])
                ->throw()->json('access_token');

            $resp = Http::withToken($token)->acceptJson()->timeout(20)->post($base.'/v1/payments/payouts', [
                'sender_batch_header' => [
                    'sender_batch_id' => 'wd_'.$withdrawal->id,
                    'email_subject' => 'Your TripCash cashback payout',
                ],
                'items' => [[
                    'recipient_type' => 'EMAIL',
                    'amount' => ['value' => number_format($withdrawal->amount, 2, '.', ''), 'currency' => $withdrawal->currency],
                    'receiver' => $details['email'] ?? $withdrawal->user->email,
                    'note' => 'Cashback payout',
                    'sender_item_id' => 'wd_'.$withdrawal->id,
                ]],
            ]);

            $body = $resp->json() ?? [];
            if ($resp->failed()) {
                return ['ok' => false, 'status' => 'failed', 'reference' => null, 'raw' => $body];
            }

            return [
                'ok' => true,
                'status' => 'processing',
                'reference' => $body['batch_header']['payout_batch_id'] ?? null,
                'raw' => $body,
            ];
        } catch (\Throwable $e) {
            Log::error('PayPal payout failed', ['wd' => $withdrawal->id, 'error' => $e->getMessage()]);

            return ['ok' => false, 'status' => 'failed', 'reference' => null, 'raw' => ['error' => $e->getMessage()]];
        }
    }
}
