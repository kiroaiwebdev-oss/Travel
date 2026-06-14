@extends('layouts.dashboard')
@section('title', 'Dashboard')
@section('heading', 'Dashboard')

@section('content')
@php $cur = $wallet->currency === 'INR' ? '₹' : '$'; @endphp

<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
    @foreach ([
        ['Withdrawable', $stats['withdrawable'], 'wallet', 'text-success'],
        ['Pending', $stats['pending'], 'clock', 'text-warning'],
        ['Lifetime earned', $stats['lifetime'], 'trending-up', 'text-primary'],
        ['Bookings', $stats['bookings'], 'ticket', 'text-ink', true],
    ] as $card)
        <div class="card p-5">
            <div class="flex items-center justify-between">
                <span class="text-sm text-muted">{{ $card[0] }}</span>
                <i data-lucide="{{ $card[2] }}" class="w-5 h-5 {{ $card[3] }}"></i>
            </div>
            <p class="mt-3 text-2xl font-extrabold font-display">{{ ($card[4] ?? false) ? number_format($card[1]) : $cur.number_format($card[1], 2) }}</p>
        </div>
    @endforeach
</div>

<div class="grid gap-5 lg:grid-cols-2 mt-6">
    <div class="card p-5">
        <div class="flex items-center justify-between mb-3">
            <h2 class="font-display font-bold">Recent cashback</h2>
            <a href="{{ route('dashboard.cashback') }}" class="text-sm text-primary font-semibold">View all</a>
        </div>
        @forelse ($recentCashback as $cb)
            <div class="flex items-center justify-between py-2.5 border-b border-slate-100 last:border-0">
                <div>
                    <p class="font-medium text-sm">{{ $cb->provider?->name ?? 'Cashback' }}</p>
                    <p class="text-xs text-muted">{{ ucfirst($cb->category) }} · {{ $cb->created_at->diffForHumans() }}</p>
                </div>
                <div class="text-right">
                    <p class="font-semibold text-success">{{ $cur }}{{ number_format($cb->amount, 2) }}</p>
                    <span class="pill pill-muted text-[11px]">{{ ucfirst($cb->status) }}</span>
                </div>
            </div>
        @empty
            <x-empty-state icon="badge-percent" text="No cashback yet. Book a trip to start earning." />
        @endforelse
    </div>

    <div class="card p-5">
        <div class="flex items-center justify-between mb-3">
            <h2 class="font-display font-bold">Recent bookings</h2>
            <a href="{{ route('dashboard.bookings') }}" class="text-sm text-primary font-semibold">View all</a>
        </div>
        @forelse ($recentBookings as $b)
            <div class="flex items-center justify-between py-2.5 border-b border-slate-100 last:border-0">
                <div>
                    <p class="font-medium text-sm">{{ $b->title ?? $b->provider?->name }}</p>
                    <p class="text-xs text-muted">{{ ucfirst($b->category) }} · {{ $b->created_at->diffForHumans() }}</p>
                </div>
                <p class="font-semibold">{{ $cur }}{{ number_format($b->amount, 0) }}</p>
            </div>
        @empty
            <x-empty-state icon="ticket" text="No bookings yet." />
        @endforelse
    </div>
</div>
@endsection
