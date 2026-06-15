<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;

class DestinationController extends Controller
{
    public function index(): View
    {
        $destinations = Destination::where('is_active', true)
            ->orderBy('sort_order')->orderBy('name')->get();

        if ($destinations->isEmpty()) {
            $destinations = $this->defaults();
        }

        return view('pages.destinations', ['destinations' => $destinations]);
    }

    /** @return Collection<int, Destination> */
    private function defaults(): Collection
    {
        return collect([
            ['Goa', 'Beaches', 'https://images.unsplash.com/photo-1512343879784-a960bf40e7f2?w=600&q=80'],
            ['Dubai', 'Luxury', 'https://images.unsplash.com/photo-1512453979798-5ea266f8880c?w=600&q=80'],
            ['Bali', 'Islands', 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?w=600&q=80'],
            ['Manali', 'Mountains', 'https://images.unsplash.com/photo-1626621341517-bbf3d9990a23?w=600&q=80'],
            ['Jaipur', 'Heritage', 'https://images.unsplash.com/photo-1599661046289-e31897846e41?w=600&q=80'],
            ['Singapore', 'City', 'https://images.unsplash.com/photo-1525625293386-3f8f99389edd?w=600&q=80'],
            ['Kerala', 'Backwaters', 'https://images.unsplash.com/photo-1602216056096-3b40cc0c9944?w=600&q=80'],
            ['Thailand', 'Tropical', 'https://images.unsplash.com/photo-1528181304800-259b08848526?w=600&q=80'],
        ])->map(fn ($d) => new Destination([
            'name' => $d[0], 'tag' => $d[1], 'image_url' => $d[2], 'category' => 'hotels',
        ]));
    }
}
