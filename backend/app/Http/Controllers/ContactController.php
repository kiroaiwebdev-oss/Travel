<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function show(): View
    {
        return view('pages.contact');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190'],
            'subject' => ['required', 'string', 'max:160'],
            'message' => ['required', 'string', 'max:3000'],
        ]);

        ContactMessage::create([
            'user_id' => $request->user()?->id,
            'name' => $data['name'],
            'email' => $data['email'],
            'subject' => $data['subject'],
            'message' => $data['message'],
            'status' => ContactMessage::NEW,
            'ip_address' => $request->ip(),
        ]);

        return back()->with('status', 'Thanks! Your message has been sent — we’ll get back to you soon.');
    }
}
