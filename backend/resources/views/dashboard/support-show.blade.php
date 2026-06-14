@extends('layouts.dashboard')
@section('title', 'Ticket '.$ticket->ticket_number)
@section('heading', 'Ticket '.$ticket->ticket_number)

@section('content')
<div class="max-w-3xl">
    <div class="card p-5 mb-4 flex items-center justify-between">
        <div>
            <h2 class="font-display font-bold">{{ $ticket->subject }}</h2>
            <p class="text-xs text-muted">{{ ucfirst($ticket->category) }} · {{ ucfirst($ticket->priority) }} priority</p>
        </div>
        <span class="pill pill-muted">{{ ucfirst($ticket->status) }}</span>
    </div>

    <div class="space-y-3">
        @foreach ($ticket->messages as $m)
            <div class="flex {{ $m->is_staff ? '' : 'justify-end' }}">
                <div class="card p-4 max-w-[80%] {{ $m->is_staff ? '' : 'bg-primary text-white' }}">
                    <p class="text-sm">{{ $m->body }}</p>
                    <p class="text-[11px] mt-1 {{ $m->is_staff ? 'text-muted' : 'text-white/70' }}">{{ $m->is_staff ? 'Support' : $m->user->name }} · {{ $m->created_at->diffForHumans() }}</p>
                </div>
            </div>
        @endforeach
    </div>

    @if (!in_array($ticket->status, ['resolved','closed']))
        <form method="POST" action="{{ route('dashboard.support.reply', $ticket) }}" class="card p-4 mt-4 flex gap-2">
            @csrf
            <input name="body" class="input" placeholder="Type your reply…" required>
            <button class="btn btn-primary">Send</button>
        </form>
    @endif
</div>
@endsection
