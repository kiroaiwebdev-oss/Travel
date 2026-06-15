<?php

namespace App\Http\Controllers;

use App\DTO\SearchQuery;
use App\Services\Search\SearchService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function __construct(private readonly SearchService $search) {}

    public function index(): View
    {
        // Featured rails are cached so the homepage stays well under the 2s budget.
        $featured = Cache::remember('home:featured', 600, function () {
            return [
                'hotels' => $this->topOffers('hotels', 'Goa', 6),
                'flights' => $this->topOffers('flights', 'Dubai', 4, origin: 'Delhi'),
                'packages' => $this->topOffers('packages', 'Thailand', 4),
            ];
        });

        $destinations = [
            ['name' => 'Goa', 'image' => 'https://picsum.photos/seed/goa/600/400', 'tag' => 'Beaches'],
            ['name' => 'Dubai', 'image' => 'https://picsum.photos/seed/dubai/600/400', 'tag' => 'Luxury'],
            ['name' => 'Bali', 'image' => 'https://picsum.photos/seed/bali/600/400', 'tag' => 'Islands'],
            ['name' => 'Manali', 'image' => 'https://picsum.photos/seed/manali/600/400', 'tag' => 'Mountains'],
            ['name' => 'Jaipur', 'image' => 'https://picsum.photos/seed/jaipur/600/400', 'tag' => 'Heritage'],
            ['name' => 'Singapore', 'image' => 'https://picsum.photos/seed/singapore/600/400', 'tag' => 'City'],
        ];

        return view('home', [
            'categories' => config('tripcash.categories'),
            'featured' => $featured,
            'destinations' => $destinations,
        ]);
    }

    private function topOffers(string $category, string $destination, int $limit, ?string $origin = null): array
    {
        return $this->search->search(SearchQuery::fromArray([
            'category' => $category,
            'destination' => $destination,
            'origin' => $origin,
            'limit' => $limit,
            'sort' => 'highest_cashback',
        ]))['offers'];
    }
}
