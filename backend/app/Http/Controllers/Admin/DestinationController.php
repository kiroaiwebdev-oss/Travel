<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Destination;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DestinationController extends Controller
{
    public function index(): View
    {
        return view('admin.destinations.index', [
            'destinations' => Destination::orderBy('sort_order')->orderBy('name')->get(),
            'categories' => config('tripcash.categories'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Destination::create($this->validated($request));

        return back()->with('status', 'Destination added.');
    }

    public function update(Request $request, Destination $destination): RedirectResponse
    {
        $destination->update($this->validated($request));

        return back()->with('status', 'Destination updated.');
    }

    public function toggle(Destination $destination): RedirectResponse
    {
        $destination->update(['is_active' => ! $destination->is_active]);

        return back()->with('status', 'Destination '.($destination->is_active ? 'shown' : 'hidden').'.');
    }

    public function destroy(Destination $destination): RedirectResponse
    {
        $destination->delete();

        return back()->with('status', 'Destination removed.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'tag' => ['nullable', 'string', 'max:40'],
            'image_url' => ['required', 'url', 'max:1024'],
            'category' => ['required', 'string', 'in:'.implode(',', array_keys(config('tripcash.categories')))],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }
}
