<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        $bookings = Booking::query()
            ->with(['user:id,name,email', 'provider:id,name'])
            ->when($request->query('status'), fn ($q, $s) => $q->where('status', $s))
            ->when($request->query('category'), fn ($q, $c) => $q->where('category', $c))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.bookings.index', [
            'bookings' => $bookings,
            'categories' => config('travelcash.categories'),
        ]);
    }

    public function updateStatus(Request $request, Booking $booking): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,confirmed,completed,cancelled,refunded'],
        ]);
        $booking->update($data);

        return back()->with('status', "Booking #{$booking->id} marked {$data['status']}.");
    }
}
