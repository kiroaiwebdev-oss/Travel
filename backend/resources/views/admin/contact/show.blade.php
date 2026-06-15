@extends('layouts.admin')
@section('title', 'Message')
@section('heading', $contact->subject)

@section('content')
<div class="max-w-3xl">
    <div class="card p-5 mb-4 flex items-center justify-between">
        <div>
            <p class="font-semibold">{{ $contact->name }} <span class="text-muted font-normal">· {{ $contact->email }}</span></p>
            <p class="text-xs text-muted">{{ $contact->created_at->format('d M Y H:i') }} · IP {{ $contact->ip_address }}</p>
        </div>
        <form method="POST" action="{{ route('admin.contact.status', $contact) }}" class="flex gap-2">
            @csrf @method('PUT')
            <select name="status" class="input py-1.5 text-sm">
                @foreach (['new','replied','closed'] as $s)<option value="{{ $s }}" @selected($contact->status===$s)>{{ ucfirst($s) }}</option>@endforeach
            </select>
            <button class="btn btn-dark text-sm">Update</button>
        </form>
    </div>

    <div class="card p-5">
        <p class="text-xs font-semibold text-muted uppercase tracking-wide">Message</p>
        <p class="mt-2 leading-relaxed">{{ $contact->message }}</p>
    </div>

    @if ($contact->admin_reply)
        <div class="card p-5 mt-4" style="background:rgba(15,98,254,.05)">
            <p class="text-xs font-semibold text-pay uppercase tracking-wide">Your reply · {{ $contact->replied_at?->diffForHumans() }}</p>
            <p class="mt-2 leading-relaxed">{{ $contact->admin_reply }}</p>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.contact.reply', $contact) }}" class="card p-5 mt-4 space-y-3">
        @csrf
        <label class="text-sm font-semibold">Reply (emailed to {{ $contact->email }})</label>
        <textarea name="reply" rows="4" class="input" required>{{ old('reply', $contact->admin_reply) }}</textarea>
        <button class="btn btn-primary"><i data-lucide="send" class="w-4 h-4"></i> Send reply</button>
    </form>
</div>
@endsection
