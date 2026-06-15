<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\Messaging\MessagingService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Admin-configurable communication channels: Email, SMS (Twilio), WhatsApp
 * Business API — plus which channel delivers OTP. Everything is toggleable.
 */
class IntegrationController extends Controller
{
    public function index(MessagingService $messaging): View
    {
        return view('admin.integrations.index', [
            'status' => $messaging->status(),
            'otpChannel' => $messaging->otpChannel(),
            'get' => fn (string $k, $d = null) => Setting::get($k, $d),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email_enabled' => ['nullable', 'boolean'],
            'sms_enabled' => ['nullable', 'boolean'],
            'whatsapp_enabled' => ['nullable', 'boolean'],
            'otp_channel' => ['required', 'in:email,sms,whatsapp'],
            'twilio_sid' => ['nullable', 'string', 'max:120'],
            'twilio_token' => ['nullable', 'string', 'max:200'],
            'twilio_from' => ['nullable', 'string', 'max:40'],
            'whatsapp_token' => ['nullable', 'string', 'max:500'],
            'whatsapp_phone_id' => ['nullable', 'string', 'max:80'],
            'mail_from_address' => ['nullable', 'email'],
            'mail_from_name' => ['nullable', 'string', 'max:120'],
        ]);

        // Toggles
        foreach (['email', 'sms', 'whatsapp'] as $ch) {
            $this->put("integrations.{$ch}.enabled", (bool) ($data["{$ch}_enabled"] ?? false), 'bool');
        }
        $this->put('integrations.otp_channel', $data['otp_channel'], 'string');

        // Credentials (only overwrite when a non-empty value is submitted)
        foreach ([
            'integrations.twilio_sid' => $data['twilio_sid'] ?? null,
            'integrations.twilio_token' => $data['twilio_token'] ?? null,
            'integrations.twilio_from' => $data['twilio_from'] ?? null,
            'integrations.whatsapp_token' => $data['whatsapp_token'] ?? null,
            'integrations.whatsapp_phone_id' => $data['whatsapp_phone_id'] ?? null,
            'integrations.mail_from_address' => $data['mail_from_address'] ?? null,
            'integrations.mail_from_name' => $data['mail_from_name'] ?? null,
        ] as $key => $val) {
            if ($val !== null && $val !== '') {
                $this->put($key, $val, 'string');
            }
        }

        return back()->with('status', 'Integration settings saved.');
    }

    public function test(Request $request, MessagingService $messaging): RedirectResponse
    {
        $data = $request->validate([
            'channel' => ['required', 'in:email,sms,whatsapp'],
            'to' => ['required', 'string', 'max:190'],
        ]);

        $result = $messaging->channel($data['channel'])->send($data['to'], 'TravelCash test message ✅', ['subject' => 'TravelCash test']);

        return back()->with('status', ($result['ok'] ? 'Test sent: ' : 'Test failed: ').$result['info']);
    }

    private function put(string $key, mixed $value, string $type): void
    {
        Setting::updateOrCreate(
            ['key' => $key],
            ['group' => 'integrations', 'value' => is_bool($value) ? ($value ? '1' : '0') : (string) $value, 'type' => $type]
        );
    }
}
