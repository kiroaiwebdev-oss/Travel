<?php

namespace App\Services\Payout;

use App\Models\Withdrawal;

/**
 * Result of a payout attempt.
 * @phpstan-type PayoutResult array{ok: bool, status: string, reference: ?string, raw: array}
 */
interface PayoutGateway
{
    public function key(): string;

    public function isConfigured(): bool;

    /**
     * Send money to the user for this withdrawal.
     *
     * @return array{ok: bool, status: string, reference: ?string, raw: array}
     */
    public function payout(Withdrawal $withdrawal): array;
}
