@extends('layouts.dashboard')
@section('title', 'Support')
@section('heading', 'Support')

@section('content')
<div class="grid lg:grid-cols-3 gap-6">
    <form method="POST" action="{{ route('dashboard.support.store') }}" class="card p-6 space-y-4">
        @csrf
        <h2 class="font-display font-bold">New ticket</h2>
        <div><label class="text-sm font-semibold">Subject</label><input name="subject" class="input mt-1" required></div>
        <div class="grid grid-cols-2 gap-3">
            <div><label class="text-sm font-semibold">Category</label>
                <select name="category" class="input mt-1"><option value="general">General</option><option value="cashback">Cashback</option><option value="booking">Booking</option><option value="payment">Payment</option></select>
            </div>
            <div><label class="text-sm font-semibold">Priority</label>
                <select name="priority" class="input mt-1"><option value="normal">Normal</option><option value="high">High</option><option value="urgent">Urgent</option><option value="low">Low</option></select>
            </div>
        </div>
        <div><label class="text-sm font-semibold">Message</label><textarea name="body" rows="4" class="input mt-1" required></textarea></div>
        <button class="btn btn-primary w-full justify-center">Submit ticket</button>
    </form>

    <div class="card overflow-hidden lg:col-span-2">
        <h2 class="font-display font-bold p-5 pb-0">Your tickets</h2>
        <div class="divide-y divide-slate-100 mt-3">
            @forelse ($tickets as $t)
                <a href="{{ route('dashboard.support.show', $t) }}" class="flex items-center justify-between p-4 hover:bg-slate-50">
                    <div>
                        <p class="font-medium">{{ $t->subject }}</p>
                        <p class="text-xs text-muted">{{ $t->ticket_number }} · {{ $t->created_at->diffForHumans() }}</p>
                    </div>
                    <span class="pill pill-muted">{{ ucfirst($t->status) }}</span>
                </a>
            @empty
                <x-empty-state icon="life-buoy" text="No tickets yet." />
            @endforelse
        </div>
        <div class="p-4">{{ $tickets->links() }}</div>
    </div>
</div>
@endsection
