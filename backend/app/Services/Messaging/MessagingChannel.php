<?php

namespace App\Services\Messaging;

interface MessagingChannel
{
    public function key(): string;

    public function isConfigured(): bool;

    /**
     * @return array{ok: bool, info: string}
     */
    public function send(string $to, string $message, array $opts = []): array;
}
