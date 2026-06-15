<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.support.index', [
            'tickets' => SupportTicket::with('user:id,name,email')
                ->when($request->query('status'), fn ($q, $s) => $q->where('status', $s))
                ->latest('last_reply_at')->paginate(25)->withQueryString(),
        ]);
    }

    public function show(SupportTicket $ticket): View
    {
        return view('admin.support.show', ['ticket' => $ticket->load('messages.user', 'user')]);
    }

    public function reply(Request $request, SupportTicket $ticket): RedirectResponse
    {
        $data = $request->validate(['body' => ['required', 'string', 'max:5000']]);
        $ticket->messages()->create([
            'user_id' => $request->user()->id,
            'is_staff' => true,
            'body' => $data['body'],
        ]);
        $ticket->update(['status' => 'pending', 'last_reply_at' => now(), 'assigned_to' => $request->user()->id]);

        return back()->with('status', 'Reply sent.');
    }

    public function updateStatus(Request $request, SupportTicket $ticket): RedirectResponse
    {
        $data = $request->validate(['status' => ['required', 'in:open,pending,resolved,closed']]);
        $ticket->update($data);

        return back()->with('status', "Ticket marked {$data['status']}.");
    }
}
