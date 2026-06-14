<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    public function index(Request $request): View
    {
        return view('dashboard.support', [
            'tickets' => $request->user()->supportTickets()->latest()->paginate(15),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'subject' => ['required', 'string', 'max:160'],
            'category' => ['nullable', 'string', 'max:40'],
            'priority' => ['nullable', 'in:low,normal,high,urgent'],
            'body' => ['required', 'string', 'max:5000'],
        ]);

        $ticket = $request->user()->supportTickets()->create([
            'subject' => $data['subject'],
            'category' => $data['category'] ?? 'general',
            'priority' => $data['priority'] ?? 'normal',
            'status' => 'open',
            'last_reply_at' => now(),
        ]);

        $ticket->messages()->create([
            'user_id' => $request->user()->id,
            'is_staff' => false,
            'body' => $data['body'],
        ]);

        return redirect()->route('dashboard.support.show', $ticket)->with('status', 'Ticket created.');
    }

    public function show(Request $request, SupportTicket $ticket): View
    {
        abort_unless($ticket->user_id === $request->user()->id, 403);

        return view('dashboard.support-show', [
            'ticket' => $ticket->load('messages.user'),
        ]);
    }

    public function reply(Request $request, SupportTicket $ticket): RedirectResponse
    {
        abort_unless($ticket->user_id === $request->user()->id, 403);

        $data = $request->validate(['body' => ['required', 'string', 'max:5000']]);

        $ticket->messages()->create([
            'user_id' => $request->user()->id,
            'is_staff' => false,
            'body' => $data['body'],
        ]);
        $ticket->update(['status' => 'pending', 'last_reply_at' => now()]);

        return back()->with('status', 'Reply sent.');
    }
}
