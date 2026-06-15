<?php

namespace App\Services\Messaging;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * WhatsApp via the Meta WhatsApp Business Cloud API.
 * https://developers.facebook.com/docs/whatsapp/cloud-api
 */
class WhatsAppChannel implements MessagingChannel
{
    public function __construct(private readonly array $cfg) {}

    public function key(): string
    {
        return 'whatsapp';
    }

    public function isConfigured(): bool
    {
        return ! empty($this->cfg['token']) && ! empty($this->cfg['phone_id']);
    }

    public function send(string $to, string $message, array $opts = []): array
    {
        if (! $this->isConfigured()) {
            return ['ok' => false, 'info' => 'WhatsApp Business API not configured'];
        }
        try {
            $resp = Http::withToken($this->cfg['token'])->acceptJson()->timeout(15)
                ->post("https://graph.facebook.com/v21.0/{$this->cfg['phone_id']}/messages", [
                    'messaging_product' => 'whatsapp',
                    'to' => preg_replace('/\D/', '', $to),
                    'type' => 'text',
                    'text' => ['body' => $message],
                ]);

            return $resp->successful()
                ? ['ok' => true, 'info' => 'WhatsApp sent']
                : ['ok' => false, 'info' => $resp->json('error.message') ?? 'WhatsApp error'];
        } catch (\Throwable $e) {
            Log::warning('WhatsApp send failed', ['to' => $to, 'error' => $e->getMessage()]);

            return ['ok' => false, 'info' => $e->getMessage()];
        }
    }
}
