<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\SavedItem;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SavedItemController extends Controller
{
    public function index(Request $request): View
    {
        $items = $request->user()->savedItems()->latest()->get()->groupBy('kind');

        return view('dashboard.saved', ['items' => $items]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'kind' => ['required', 'in:saved_hotel,saved_flight,saved_search,watchlist'],
            'category' => ['nullable', 'string', 'max:20'],
            'reference' => ['nullable', 'string', 'max:190'],
            'payload' => ['required', 'array'],
            'target_price' => ['nullable', 'numeric', 'min:0'],
        ]);

        $request->user()->savedItems()->create($data);

        return back()->with('status', 'Saved.');
    }

    public function destroy(Request $request, SavedItem $savedItem): RedirectResponse
    {
        abort_unless($savedItem->user_id === $request->user()->id, 403);
        $savedItem->delete();

        return back()->with('status', 'Removed.');
    }
}
