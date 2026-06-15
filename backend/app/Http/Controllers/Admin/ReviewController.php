<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->get('status', 'pending');
        $type = $request->get('type');

        $reviews = Review::with('user')
            ->when(in_array($status, ['pending', 'approved', 'rejected'], true), fn ($q) => $q->where('status', $status))
            ->when(in_array($type, ['review', 'suggestion'], true), fn ($q) => $q->where('type', $type))
            ->latest()->paginate(20)->withQueryString();

        return view('admin.reviews.index', [
            'reviews' => $reviews,
            'status' => $status,
            'type' => $type,
            'counts' => [
                'pending' => Review::where('status', 'pending')->count(),
                'approved' => Review::where('status', 'approved')->count(),
                'rejected' => Review::where('status', 'rejected')->count(),
            ],
        ]);
    }

    public function approve(Review $review): RedirectResponse
    {
        $review->update(['status' => Review::APPROVED]);

        return back()->with('status', 'Review approved — now visible on the site.');
    }

    public function reject(Review $review): RedirectResponse
    {
        $review->update(['status' => Review::REJECTED, 'is_featured' => false]);

        return back()->with('status', 'Review rejected.');
    }

    public function feature(Review $review): RedirectResponse
    {
        $review->update(['is_featured' => ! $review->is_featured]);

        return back()->with('status', $review->is_featured ? 'Featured on landing page.' : 'Removed from landing page.');
    }

    public function destroy(Review $review): RedirectResponse
    {
        $review->delete();

        return back()->with('status', 'Review deleted.');
    }
}
