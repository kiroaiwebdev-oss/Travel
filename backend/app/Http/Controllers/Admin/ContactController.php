<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Services\Messaging\MessagingService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.contact.index', [
            'messages' => ContactMessage::query()
                ->when($request->query('status'), fn ($q, $s) => $q->where('status', $s))
                ->latest()->paginate(20)->withQueryString(),
            'newCount' => ContactMessage::where('status', 'new')->count(),
        ]);
    }

    public function show(ContactMessage $contact): View
    {
        return view('admin.contact.show', ['contact' => $contact->load('repliedBy')]);
    }

    public function reply(Request $request, ContactMessage $contact, MessagingService $messaging): RedirectResponse
    {
        $data = $request->validate(['reply' => ['required', 'string', 'max:5000']]);

        // Email the reply to the sender (uses the configured mailer).
        $result = $messaging->channel('email')->send($contact->email, $data['reply'], [
            'subject' => 'Re: '.$contact->subject,
        ]);

        $contact->update([
            'admin_reply' => $data['reply'],
            'status' => ContactMessage::REPLIED,
            'replied_by' => $request->user()->id,
            'replied_at' => now(),
        ]);

        return back()->with('status', $result['ok']
            ? 'Reply sent to '.$contact->email
            : 'Saved, but email delivery: '.$result['info']);
    }

    public function updateStatus(Request $request, ContactMessage $contact): RedirectResponse
    {
        $data = $request->validate(['status' => ['required', 'in:new,replied,closed']]);
        $contact->update($data);

        return back()->with('status', "Marked {$data['status']}.");
    }
}
