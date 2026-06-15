<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /** Public submission of a review or suggestion (goes to moderation). */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'location' => ['nullable', 'string', 'max:120'],
            'type' => ['required', 'in:review,suggestion'],
            'rating' => ['nullable', 'integer', 'min:1', 'max:5'],
            'message' => ['required', 'string', 'min:5', 'max:1500'],
        ]);

        Review::create([
            'user_id' => $request->user()?->id,
            'name' => $data['name'],
            'location' => $data['location'] ?? null,
            'rating' => $data['type'] === 'review' ? ($data['rating'] ?? 5) : null,
            'type' => $data['type'],
            'message' => $data['message'],
            'status' => Review::PENDING,
        ]);

        $label = $data['type'] === 'suggestion' ? 'suggestion' : 'review';

        return back()->with('status', "Thank you! Your {$label} has been submitted — it will appear once approved.");
    }
}
