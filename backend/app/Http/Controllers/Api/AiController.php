<?php

namespace App\Http\Controllers\Api;

use App\DTO\SearchQuery;
use App\Http\Controllers\Controller;
use App\Services\Search\SearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AiController extends Controller
{
    public function __construct(private readonly SearchService $search) {}

    /**
     * Travel assistant. Laravel grounds the AI with real platform offers, then asks
     * the FastAPI sidecar (which fans out across Groq -> Gemini -> OpenAI) to answer.
     */
    public function assistant(Request $request): JsonResponse
    {
        $data = $request->validate([
            'message' => ['required', 'string', 'max:1000'],
            'category' => ['nullable', 'string'],
            'destination' => ['nullable', 'string', 'max:120'],
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

        try {
            $response = Http::timeout((int) config('services.ai.timeout', 30))
                ->acceptJson()
                ->post($base.'/assistant', [
                    'message' => $data['message'],
                    'context' => $context,
                    'user_id' => $request->user()?->id,
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
