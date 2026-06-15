<?php

namespace App\Services\Messaging;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Log;

/**
 * Central outbound messaging. Resolves effective channel config from admin
 * Settings (overriding env/config), exposes channel status, the active OTP
 * channel, and sends messages to users via email / SMS / WhatsApp.
 */
class MessagingService
{
    /** Effective credential config (admin Setting overrides env/config). */
    private function cfg(): array
    {
        return [
            'twilio' => [
                'sid' => Setting::get('integrations.twilio_sid') ?: config('services.twilio.sid'),
                'token' => Setting::get('integrations.twilio_token') ?: config('services.twilio.token'),
                'from' => Setting::get('integrations.twilio_from') ?: config('services.twilio.from'),
            ],
            'whatsapp' => [
                'token' => Setting::get('integrations.whatsapp_token') ?: config('services.whatsapp.token'),
                'phone_id' => Setting::get('integrations.whatsapp_phone_id') ?: config('services.whatsapp.phone_id'),
            ],
            'email' => [],
        ];
    }

    public function channel(string $key): MessagingChannel
    {
        $cfg = $this->cfg();

        return match ($key) {
            'sms' => new SmsChannel($cfg['twilio']),
            'whatsapp' => new WhatsAppChannel($cfg['whatsapp']),
            default => new EmailChannel($cfg['email']),
        };
    }

    /** @return array<string, array{configured: bool, enabled: bool}> */
    public function status(): array
    {
        $out = [];
        foreach (['email', 'sms', 'whatsapp'] as $key) {
            $ch = $this->channel($key);
            $out[$key] = ['configured' => $ch->isConfigured(), 'enabled' => $this->enabled($key)];
        }

        return $out;
    }

    public function enabled(string $key): bool
    {
        $default = $key === 'email'; // email on by default; others off until configured
        $val = Setting::get("integrations.{$key}.enabled");

        return $val === null ? $default : (bool) $val;
    }

    public function otpChannel(): string
    {
        $ch = (string) (Setting::get('integrations.otp_channel') ?: 'email');

        return in_array($ch, ['email', 'sms', 'whatsapp'], true) ? $ch : 'email';
    }

    /** Resolve the recipient address for a channel. */
    private function addressFor(User $user, string $channel): ?string
    {
        return $channel === 'email' ? $user->email : $user->phone;
    }

    /**
     * Send a free-form message to a user via a channel (defaults to the first
     * enabled channel). Returns the channel result.
     */
    public function sendToUser(User $user, string $message, ?string $channel = null, ?string $subject = null): array
    {
        $channel ??= collect(['email', 'sms', 'whatsapp'])->first(fn ($c) => $this->enabled($c)) ?? 'email';
        $to = $this->addressFor($user, $channel);

        if (! $to) {
            return ['ok' => false, 'info' => "User has no {$channel} address on file"];
        }

        $result = $this->channel($channel)->send($to, $message, ['subject' => $subject ?? 'A message from TripCash']);
        Log::info('Outbound message', ['user' => $user->id, 'channel' => $channel, 'ok' => $result['ok']]);

        return $result + ['channel' => $channel];
    }

    /** Send a login/verification OTP through the admin-selected channel. */
    public function sendOtp(User $user, string $code): array
    {
        $channel = $this->otpChannel();
        $to = $this->addressFor($user, $channel);
        $message = "Your TripCash verification code is {$code}. It expires in 10 minutes.";

        if (! $to) {
            // Fall back to email if the chosen channel has no address.
            $channel = 'email';
            $to = $user->email;
        }

        return $this->channel($channel)->send($to, $message, ['subject' => 'Your TripCash code']) + ['channel' => $channel];
    }
}
