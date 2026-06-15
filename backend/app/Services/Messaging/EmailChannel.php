<?php

namespace App\Services\Messaging;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailChannel implements MessagingChannel
{
    public function __construct(private readonly array $cfg) {}

    public function key(): string
    {
        return 'email';
    }

    public function isConfigured(): bool
    {
        // Email always works (falls back to the "log" mailer in dev).
        return true;
    }

    public function send(string $to, string $message, array $opts = []): array
    {
        try {
            $subject = $opts['subject'] ?? 'TravelCash';
            Mail::raw($message, function ($m) use ($to, $subject) {
                $m->to($to)->subject($subject);
            });

            return ['ok' => true, 'info' => 'email sent to '.$to];
        } catch (\Throwable $e) {
            Log::warning('Email send failed', ['to' => $to, 'error' => $e->getMessage()]);

            return ['ok' => false, 'info' => $e->getMessage()];
        }
    }
}
