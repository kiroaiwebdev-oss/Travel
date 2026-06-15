<?php

namespace App\Http\Controllers;

use App\DTO\SearchQuery;
use App\Http\Requests\SearchRequest;
use App\Services\Search\SearchService;
use Illuminate\Contracts\View\View;

class SearchController extends Controller
{
    public function __construct(private readonly SearchService $search) {}

    public function index(SearchRequest $request): View
    {
        $query = SearchQuery::fromArray($request->validated());

        $result = $this->search->search(
            $query,
            userId: $request->user()?->id,
            sessionId: $request->session()->getId(),
        );

        return view('search', [
            'query' => $query,
            'offers' => $result['offers'],
            'meta' => $result['meta'],
            'categories' => config('tripcash.categories'),
        ]);
    }
}
