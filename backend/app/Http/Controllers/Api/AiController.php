<?php

namespace App\Http\Controllers\Api;

use App\DTO\SearchQuery;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\Search\SearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AiController extends Controller
{
    public function __construct(private readonly SearchService $search) {}

    /**
     * Travel assistant. Laravel grounds the AI with real platform offers and applies
     * the admin-configured prompt / provider keys / priority, then asks the FastAPI
     * sidecar (Groq -> Gemini -> OpenAI -> demo) to answer.
     */
    public function assistant(Request $request): JsonResponse
    {
        if (! (bool) Setting::get('ai.enabled', true)) {
            return response()->json(['message' => 'The AI assistant is currently turned off.'], 503);
        }

        $data = $request->validate([
            'message' => ['required', 'string', 'max:1000'],
            'category' => ['nullable', 'string'],
            'destination' => ['nullable', 'string', 'max:120'],
            'history' => ['nullable', 'array', 'max:20'],
            'history.*.role' => ['required_with:history', 'in:user,assistant'],
            'history.*.content' => ['required_with:history', 'string', 'max:2000'],
        ]);

        // Ground the model with up to a handful of real, cashback-enriched offers.
        $context = [];
        if (! empty($data['category'])) {
            $result = $this->search->search(SearchQuery::fromArray([
                'category' => $data['category'],
                'destination' => $data['destination'] ?? null,
                'limit' => 6,
                'sort' => 'best_value',
            ]));
            $context = $result['offers'];
        }

        $base = rtrim((string) config('services.ai.base_url'), '/');

        // Admin-configured overrides.
        $keys = array_filter([
            'groq' => (string) Setting::get('ai.groq_key', ''),
            'gemini' => (string) Setting::get('ai.gemini_key', ''),
            'openai' => (string) Setting::get('ai.openai_key', ''),
        ], fn ($v) => $v !== '');

        $priority = array_values(array_filter(array_map(
            'trim',
            explode(',', (string) Setting::get('ai.provider_priority', 'groq,gemini,openai'))
        )));

        $suggestions = array_values(array_filter(array_map(
            'trim',
            preg_split('/\r\n|\r|\n/', (string) Setting::get('ai.suggestions', ''))
        )));

        try {
            $response = Http::timeout((int) config('services.ai.timeout', 30))
                ->acceptJson()
                ->post($base.'/assistant', [
                    'message' => $data['message'],
                    'context' => $context,
                    'history' => $data['history'] ?? [],
                    'user_id' => $request->user()?->id,
                    'system_prompt' => (string) Setting::get('ai.system_prompt', '') ?: null,
                    'keys' => (object) $keys,
                    'priority' => $priority ?: null,
                    'suggestions' => $suggestions ?: null,
                ]);

            if ($response->failed()) {
                return response()->json(['message' => 'AI service unavailable. Please try again.'], 503);
            }

            return response()->json($response->json());
        } catch (\Throwable $e) {
            return response()->json(['message' => 'AI service unreachable.'], 503);
        }
    }
}
