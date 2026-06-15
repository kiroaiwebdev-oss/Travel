<?php

namespace App\Services\Payout;

use App\Models\Withdrawal;
use Illuminate\Support\Str;

/**
 * Default gateway: the admin transfers money out-of-band (manual bank/UPI) and the
 * system records it. Always available, no API keys needed.
 */
class ManualGateway implements PayoutGateway
{
    public function key(): string
    {
        return 'manual';
    }

    public function isConfigured(): bool
    {
        return true;
    }

    public function payout(Withdrawal $withdrawal): array
    {
        return [
            'ok' => true,
            'status' => 'processing',
            'reference' => 'MANUAL-'.strtoupper(Str::random(8)),
            'raw' => ['note' => 'Marked for manual transfer by admin.'],
        ];
    }
}
