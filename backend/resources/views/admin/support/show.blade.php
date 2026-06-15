@extends('layouts.admin')
@section('title', 'Ticket '.$ticket->ticket_number)
@section('heading', $ticket->subject)

@section('content')
<div class="max-w-3xl">
    <div class="card p-5 mb-4 flex items-center justify-between">
        <div>
            <p class="text-sm text-muted">{{ $ticket->ticket_number }} · {{ $ticket->user?->name }} ({{ $ticket->user?->email }})</p>
            <p class="text-xs text-muted">{{ ucfirst($ticket->category) }} · {{ ucfirst($ticket->priority) }} priority</p>
        </div>
        <form method="POST" action="{{ route('admin.support.status', $ticket) }}" class="flex gap-2">
            @csrf @method('PUT')
            <select name="status" class="input py-1.5 text-sm">
                @foreach (['open','pending','resolved','closed'] as $s)<option value="{{ $s }}" @selected($ticket->status===$s)>{{ ucfirst($s) }}</option>@endforeach
            </select>
            <button class="btn btn-dark text-sm">Update</button>
        </form>
    </div>

    <div class="space-y-3">
        @foreach ($ticket->messages as $m)
            <div class="flex {{ $m->is_staff ? 'justify-end' : '' }}">
                <div class="card p-4 max-w-[80%] {{ $m->is_staff ? 'bg-pay text-white' : '' }}" @if($m->is_staff) style="background:#0F62FE;color:#fff" @endif>
                    <p class="text-sm">{{ $m->body }}</p>
                    <p class="text-[11px] mt-1 {{ $m->is_staff ? 'text-white/70' : 'text-muted' }}">{{ $m->is_staff ? 'Support · '.$m->user?->name : $m->user?->name }} · {{ $m->created_at->diffForHumans() }}</p>
                </div>
            </div>
        @endforeach
    </div>

    <form method="POST" action="{{ route('admin.support.reply', $ticket) }}" class="card p-4 mt-4 flex gap-2">
        @csrf
        <input name="body" class="input" placeholder="Reply to the customer…" required>
        <button class="btn btn-primary">Send</button>
    </form>
</div>
@endsection
