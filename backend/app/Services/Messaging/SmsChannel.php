<?php

namespace App\Services\Messaging;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * SMS via Twilio. Activates when SID/token/from are configured.
 * https://www.twilio.com/docs/sms/send-messages
 */
class SmsChannel implements MessagingChannel
{
    public function __construct(private readonly array $cfg) {}

    public function key(): string
    {
        return 'sms';
    }

    public function isConfigured(): bool
    {
        return ! empty($this->cfg['sid']) && ! empty($this->cfg['token']) && ! empty($this->cfg['from']);
    }

    public function send(string $to, string $message, array $opts = []): array
    {
        if (! $this->isConfigured()) {
            return ['ok' => false, 'info' => 'Twilio SMS not configured'];
        }
        try {
            $resp = Http::asForm()->withBasicAuth($this->cfg['sid'], $this->cfg['token'])->timeout(15)
                ->post("https://api.twilio.com/2010-04-01/Accounts/{$this->cfg['sid']}/Messages.json", [
                    'From' => $this->cfg['from'],
                    'To' => $to,
                    'Body' => $message,
                ]);

            return $resp->successful()
                ? ['ok' => true, 'info' => 'SMS sent (sid '.($resp->json('sid') ?? '').')']
                : ['ok' => false, 'info' => $resp->json('message') ?? 'Twilio error'];
        } catch (\Throwable $e) {
            Log::warning('SMS send failed', ['to' => $to, 'error' => $e->getMessage()]);

            return ['ok' => false, 'info' => $e->getMessage()];
        }
    }
}
