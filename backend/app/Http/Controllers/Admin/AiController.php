<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * Full admin control of the AI travel assistant: enable/disable, branding,
 * welcome message, system prompt, quick-suggestions, provider API keys and
 * fallback priority — plus a live "test" that pings the FastAPI sidecar.
 */
class AiController extends Controller
{
    public function index(): View
    {
        $base = rtrim((string) config('services.ai.base_url'), '/');
        $health = null;
        try {
            $resp = Http::timeout(4)->acceptJson()->get($base.'/health');
            $health = $resp->ok() ? $resp->json() : null;
        } catch (\Throwable) {
            $health = null;
        }

        return view('admin.ai.index', [
            'get' => fn (string $k, $d = null) => Setting::get($k, $d),
            'health' => $health,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'enabled' => ['nullable', 'boolean'],
            'assistant_name' => ['nullable', 'string', 'max:60'],
            'welcome_message' => ['nullable', 'string', 'max:500'],
            'system_prompt' => ['nullable', 'string', 'max:3000'],
            'suggestions' => ['nullable', 'string', 'max:1200'],
            'provider_priority' => ['nullable', 'string', 'max:60'],
            'groq_key' => ['nullable', 'string', 'max:200'],
            'gemini_key' => ['nullable', 'string', 'max:200'],
            'openai_key' => ['nullable', 'string', 'max:200'],
        ]);

        $this->put('ai.enabled', (bool) ($data['enabled'] ?? false), 'bool');

        foreach (['assistant_name', 'welcome_message', 'system_prompt', 'suggestions', 'provider_priority'] as $k) {
            $this->put("ai.{$k}", $data[$k] ?? '', 'string');
        }

        // API keys: only overwrite when a non-empty value is submitted (so masked
        // fields left blank keep the existing key).
        foreach (['groq_key', 'gemini_key', 'openai_key'] as $k) {
            if (! empty($data[$k])) {
                $this->put("ai.{$k}", $data[$k], 'string');
            }
        }

        return back()->with('status', 'AI assistant settings saved.');
    }

    public function test(Request $request): RedirectResponse
    {
        $base = rtrim((string) config('services.ai.base_url'), '/');

        $keys = array_filter([
            'groq' => (string) Setting::get('ai.groq_key', ''),
            'gemini' => (string) Setting::get('ai.gemini_key', ''),
            'openai' => (string) Setting::get('ai.openai_key', ''),
        ], fn ($v) => $v !== '');

        $priority = array_values(array_filter(array_map('trim', explode(',', (string) Setting::get('ai.provider_priority', 'groq,gemini,openai')))));

        try {
            $resp = Http::timeout((int) config('services.ai.timeout', 30))->acceptJson()->post($base.'/assistant', [
                'message' => $request->input('message', 'Suggest a cheap beach hotel in Goa with good cashback.'),
                'context' => [],
                'system_prompt' => (string) Setting::get('ai.system_prompt', '') ?: null,
                'keys' => (object) $keys,
                'priority' => $priority ?: null,
            ]);

            if ($resp->failed()) {
                return back()->with('status', 'AI test failed — sidecar returned an error.');
            }

            $json = $resp->json();
            $provider = $json['provider_used'] ?? 'unknown';
            $reply = \Illuminate\Support\Str::limit($json['message'] ?? '', 160);

            return back()->with('status', "AI replied via “{$provider}”: {$reply}");
        } catch (\Throwable $e) {
            return back()->with('status', 'AI service unreachable. Is the ai container running?');
        }
    }

    private function put(string $key, mixed $value, string $type): void
    {
        Setting::updateOrCreate(
            ['key' => $key],
            [
                'group' => 'ai',
                'value' => is_bool($value) ? ($value ? '1' : '0') : (string) $value,
                'type' => $type,
                'is_public' => in_array($key, ['ai.enabled', 'ai.assistant_name', 'ai.welcome_message', 'ai.suggestions'], true),
            ]
        );
    }
}
