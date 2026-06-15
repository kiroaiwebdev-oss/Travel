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
     * Travel assistant. Laravel grounds the AI with real, cashback + affiliate-link
     * enriched offers and applies the admin-configured prompt / keys / priority, then
     * asks the FastAPI sidecar to answer. Real offer cards (with affiliate links) are
     * returned alongside the reply so suggestions are always monetised.
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

        // Resolve category/destination — prefer what the UI sent, else infer from the text.
        $category = $data['category'] ?: $this->detectCategory($data['message']);
        $destination = $data['destination'] ?: $this->detectDestination($data['message']);
        $origin = $this->detectOrigin($data['message']);

        // Ground the model with real, cashback + affiliate-enriched offers.
        $context = [];
        if ($category) {
            $result = $this->search->search(SearchQuery::fromArray([
                'category' => $category,
                'destination' => $destination,
                'origin' => $origin,
                'limit' => 6,
                'sort' => 'best_value',
            ]));
            $context = $result['offers'];
        }

        // Compact offer cards (with affiliate go_url) for the chat UI.
        $offerCards = collect($context)->take(5)->map(fn ($o) => [
            'title' => $o['title'] ?? '',
            'provider_name' => $o['provider_name'] ?? '',
            'price' => $o['price'] ?? null,
            'cashback' => $o['cashback'] ?? null,
            'currency' => $o['currency'] ?? 'INR',
            'rating' => $o['rating'] ?? null,
            'image' => $o['images'][0] ?? null,
            'go_url' => $o['go_url'] ?? null,         // signed affiliate redirect
            'category' => $o['category'] ?? $category,
        ])->values()->all();

        $base = rtrim((string) config('services.ai.base_url'), '/');

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
                return response()->json(['message' => 'AI service unavailable. Please try again.', 'offers' => $offerCards], 200);
            }

            return response()->json(array_merge($response->json(), [
                'offers' => $offerCards,
                'category' => $category,
                'destination' => $destination,
            ]));
        } catch (\Throwable $e) {
            // Even if the AI text fails, still return real affiliate offers if we found any.
            return response()->json([
                'message' => $offerCards
                    ? 'Here are some great options I found for you:'
                    : 'The AI service is unreachable right now. Please try again in a moment.',
                'offers' => $offerCards,
            ], 200);
        }
    }

    private function detectCategory(string $msg): ?string
    {
        $m = ' '.strtolower($msg).' ';
        $map = [
            'flights' => ['flight', 'fly ', 'airfare', 'airline', 'air ticket'],
            'trains' => ['train', 'rail', 'irctc'],
            'cabs' => ['cab', 'taxi', 'ride', 'transfer', 'uber', 'ola'],
            'packages' => ['package', 'holiday', 'tour ', 'itinerary', 'honeymoon'],
            'hotels' => ['hotel', 'stay', 'resort', 'room', 'accommodation', 'lodge', 'villa'],
        ];
        foreach ($map as $cat => $words) {
            foreach ($words as $w) {
                if (str_contains($m, $w)) {
                    return $cat;
                }
            }
        }

        return null;
    }

    private function detectDestination(string $msg): ?string
    {
        // Capture 1-2 words after in/at/near/to (e.g. "hotels in Bengaluru").
        if (preg_match('/\b(?:in|at|near|to|for)\s+([a-zA-Z][a-zA-Z]+(?:\s[A-Z][a-zA-Z]+)?)/', $msg, $m)) {
            $candidate = trim($m[1]);
            // Avoid grabbing filler words.
            if (! in_array(strtolower($candidate), ['the', 'a', 'an', 'me', 'my', 'best', 'cheap'], true)) {
                return ucwords($candidate);
            }
        }

        return null;
    }

    private function detectOrigin(string $msg): ?string
    {
        if (preg_match('/\bfrom\s+([a-zA-Z][a-zA-Z]+(?:\s[A-Z][a-zA-Z]+)?)\s+to\b/i', $msg, $m)) {
            return ucwords(trim($m[1]));
        }

        return null;
    }
}
