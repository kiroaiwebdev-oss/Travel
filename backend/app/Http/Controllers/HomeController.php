<?php

namespace App\Http\Controllers;

use App\DTO\SearchQuery;
use App\Models\Destination;
use App\Models\Offer;
use App\Models\Review;
use App\Services\Search\SearchService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function __construct(private readonly SearchService $search) {}

    public function index(): View
    {
        // Demo featured rails (fallback when admin hasn't curated offers for a category).
        $featured = Cache::remember('home:featured', 600, function () {
            return [
                'hotels' => $this->topOffers('hotels', 'Goa', 6),
                'flights' => $this->topOffers('flights', 'Dubai', 4, origin: 'Delhi'),
                'packages' => $this->topOffers('packages', 'Thailand', 4),
            ];
        });

        // Admin-curated featured offers (override the demo rails when present).
        $featuredOffers = Offer::active()->where('is_featured', true)
            ->with('provider')->orderBy('sort_order')->latest()->get()->groupBy('category');

        // Admin-managed trending destinations (fallback to sensible defaults).
        $destinations = Destination::active()->orderBy('sort_order')->orderBy('name')->get();
        if ($destinations->isEmpty()) {
            $destinations = $this->defaultDestinations();
        }

        // Approved reviews — featured first, otherwise most recent approved.
        $reviews = Review::approved()->reviews()->where('is_featured', true)->latest()->take(9)->get();
        if ($reviews->isEmpty()) {
            $reviews = Review::approved()->reviews()->latest()->take(9)->get();
        }

        return view('home', [
            'categories' => config('tripcash.categories'),
            'featured' => $featured,
            'featuredOffers' => $featuredOffers,
            'destinations' => $destinations,
            'reviews' => $reviews,
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

    /** @return Collection<int, Destination> */
    private function defaultDestinations(): Collection
    {
        return collect([
            ['Goa', 'Beaches', 'https://images.unsplash.com/photo-1512343879784-a960bf40e7f2?w=600&q=80'],
            ['Dubai', 'Luxury', 'https://images.unsplash.com/photo-1512453979798-5ea266f8880c?w=600&q=80'],
            ['Bali', 'Islands', 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?w=600&q=80'],
            ['Manali', 'Mountains', 'https://images.unsplash.com/photo-1626621341517-bbf3d9990a23?w=600&q=80'],
            ['Jaipur', 'Heritage', 'https://images.unsplash.com/photo-1599661046289-e31897846e41?w=600&q=80'],
            ['Singapore', 'City', 'https://images.unsplash.com/photo-1525625293386-3f8f99389edd?w=600&q=80'],
        ])->map(fn ($d) => new Destination([
            'name' => $d[0], 'tag' => $d[1], 'image_url' => $d[2], 'category' => 'hotels',
        ]));
    }
}
