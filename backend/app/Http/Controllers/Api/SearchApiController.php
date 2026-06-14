<?php

namespace App\Http\Controllers\Api;

use App\DTO\SearchQuery;
use App\Http\Controllers\Controller;
use App\Http\Requests\SearchRequest;
use App\Services\Search\SearchService;
use Illuminate\Http\JsonResponse;

class SearchApiController extends Controller
{
    public function __construct(private readonly SearchService $search) {}

    public function search(SearchRequest $request): JsonResponse
    {
        $query = SearchQuery::fromArray($request->validated());

        $result = $this->search->search(
            $query,
            userId: $request->user()?->id,
            sessionId: $request->session()?->getId(),
        );

        return response()->json([
            'data' => $result['offers'],
            'meta' => $result['meta'],
        ]);
    }

    public function categories(): JsonResponse
    {
        return response()->json(['data' => config('travelcash.categories')]);
    }
}
