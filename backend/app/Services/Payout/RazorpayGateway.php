<?php

namespace App\Services\Payout;

use App\Models\Withdrawal;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Razorpay Payouts (UPI/Bank) — India. Activates automatically once the admin adds
 * keys in config/.env. Falls back to "not configured" otherwise.
 *
 * Docs: https://razorpay.com/docs/x/payouts/
 */
class RazorpayGateway implements PayoutGateway
{
    public function key(): string
    {
        return 'razorpay';
    }

    public function isConfigured(): bool
    {
        return ! empty(config('services.razorpay.key')) && ! empty(config('services.razorpay.secret'));
    }

    public function payout(Withdrawal $withdrawal): array
    {
        $cfg = config('services.razorpay');
        $details = $withdrawal->payout_details ?? [];

        $payload = [
            'account_number' => $cfg['account_number'],
            'amount' => (int) round($withdrawal->amount * 100), // paise
            'currency' => $withdrawal->currency,
            'mode' => $withdrawal->method === 'bank' ? 'IMPS' : 'UPI',
            'purpose' => 'cashback',
            'fund_account' => $this->fundAccount($withdrawal, $details),
            'queue_if_low_balance' => true,
            'reference_id' => 'wd_'.$withdrawal->id,
            'narration' => 'TravelCash cashback payout',
        ];

        try {
            $resp = Http::withBasicAuth($cfg['key'], $cfg['secret'])
                ->acceptJson()
                ->timeout(20)
                ->post('https://api.razorpay.com/v1/payouts', $payload);

            $body = $resp->json() ?? [];
            if ($resp->failed()) {
                return ['ok' => false, 'status' => 'failed', 'reference' => null, 'raw' => $body];
            }

            return [
                'ok' => true,
                'status' => $body['status'] ?? 'processing',
                'reference' => $body['id'] ?? null,
                'raw' => $body,
            ];
        } catch (\Throwable $e) {
            Log::error('Razorpay payout failed', ['wd' => $withdrawal->id, 'error' => $e->getMessage()]);

            return ['ok' => false, 'status' => 'failed', 'reference' => null, 'raw' => ['error' => $e->getMessage()]];
        }
    }

    private function fundAccount(Withdrawal $withdrawal, array $details): array
    {
        if ($withdrawal->method === 'upi') {
            return [
                'account_type' => 'vpa',
                'vpa' => ['address' => $details['upi'] ?? ''],
            ];
        }

        return [
            'account_type' => 'bank_account',
            'bank_account' => [
                'name' => $details['name'] ?? $withdrawal->user->name,
                'ifsc' => $details['ifsc'] ?? '',
                'account_number' => $details['account'] ?? '',
            ],
        ];
    }
}
