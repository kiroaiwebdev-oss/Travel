@extends('layouts.dashboard')
@section('title', 'Dashboard')
@section('heading', 'Dashboard')

@section('content')
@php $cur = $wallet->currency === 'INR' ? '₹' : '$'; @endphp

{{-- ===== Mobile balance hero (app) ===== --}}
<div class="lg:hidden">
    <div class="app-balance p-5">
        <div class="relative">
            <div class="flex items-center justify-between">
                <p class="text-sm text-white/70">Withdrawable balance</p>
                <a href="{{ route('dashboard.wallet') }}" class="text-xs font-semibold text-white/80 flex items-center gap-1">Wallet <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i></a>
            </div>
            <p class="text-4xl font-extrabold font-display mt-1">{{ $cur }}{{ number_format($stats['withdrawable'], 2) }}</p>
            <div class="mt-4 flex gap-2">
                <a href="{{ route('dashboard.withdrawals') }}" class="press flex-1 text-center rounded-xl py-2.5 font-bold text-sm bg-white text-ink">Withdraw</a>
                <a href="{{ route('home') }}" class="press flex-1 text-center rounded-xl py-2.5 font-bold text-sm" style="background:rgba(255,255,255,.15);color:#fff">Earn more</a>
            </div>
            <div class="mt-4 grid grid-cols-2 gap-3">
                <div class="rounded-xl p-3" style="background:rgba(255,255,255,.08)">
                    <p class="text-xs text-white/60">Pending</p>
                    <p class="font-bold mt-0.5">{{ $cur }}{{ number_format($stats['pending'], 2) }}</p>
                </div>
                <div class="rounded-xl p-3" style="background:rgba(255,255,255,.08)">
                    <p class="text-xs text-white/60">Lifetime earned</p>
                    <p class="font-bold mt-0.5">{{ $cur }}{{ number_format($stats['lifetime'], 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick actions --}}
    <div class="grid grid-cols-4 gap-2 mt-5">
        @foreach ([
            ['dashboard.cashback', 'Cashback', 'badge-percent', 'background:rgba(34,197,94,.12);color:#16a34a'],
            ['dashboard.bookings', 'Bookings', 'ticket', 'background:rgba(15,98,254,.1);color:#0F62FE'],
            ['dashboard.saved', 'Saved', 'heart', 'background:rgba(236,72,153,.12);color:#db2777'],
            ['dashboard.referrals', 'Refer', 'users', 'background:rgba(255,138,0,.12);color:#c2410c'],
            ['dashboard.withdrawals', 'Withdraw', 'banknote', 'background:rgba(168,85,247,.12);color:#9333ea'],
            ['dashboard.kyc', 'KYC', 'shield-check', 'background:rgba(0,184,169,.12);color:#009688'],
            ['dashboard.support', 'Support', 'life-buoy', 'background:rgba(100,116,139,.12);color:#475569'],
            ['dashboard.profile', 'Settings', 'settings', 'background:rgba(30,41,59,.08);color:#1E293B'],
        ] as $qa)
            <a href="{{ route($qa[0]) }}" class="qa press">
                <span class="qa-ic" style="{{ $qa[3] }}"><i data-lucide="{{ $qa[2] }}" class="w-5 h-5"></i></span>
                <span>{{ $qa[1] }}</span>
            </a>
        @endforeach
    </div>
</div>

{{-- ===== Desktop stat cards ===== --}}
<div class="hidden lg:grid gap-4 grid-cols-4">
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

{{-- ===== Recent activity ===== --}}
<div class="grid gap-5 lg:grid-cols-2 mt-6">
    {{-- Recent cashback --}}
    <div>
        <div class="flex items-center justify-between mb-2 px-1">
            <h2 class="font-display font-bold">Recent cashback</h2>
            <a href="{{ route('dashboard.cashback') }}" class="text-sm text-primary font-semibold">View all</a>
        </div>
        <div class="list-group">
            @forelse ($recentCashback as $cb)
                <div class="list-row">
                    <span class="list-row-ic" style="background:rgba(34,197,94,.1);color:#16a34a"><i data-lucide="badge-percent" class="w-4 h-4"></i></span>
                    <div class="min-w-0 flex-1">
                        <p class="list-row-title truncate">{{ $cb->provider?->name ?? 'Cashback' }}</p>
                        <p class="list-row-sub">{{ ucfirst($cb->category) }} · {{ $cb->created_at->diffForHumans() }}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold text-success">{{ $cur }}{{ number_format($cb->amount, 2) }}</p>
                        <span class="pill pill-muted text-[11px]">{{ ucfirst($cb->status) }}</span>
                    </div>
                </div>
            @empty
                <div class="p-2"><x-empty-state icon="badge-percent" text="No cashback yet. Book a trip to start earning." /></div>
            @endforelse
        </div>
    </div>

    {{-- Recent bookings --}}
    <div>
        <div class="flex items-center justify-between mb-2 px-1">
            <h2 class="font-display font-bold">Recent bookings</h2>
            <a href="{{ route('dashboard.bookings') }}" class="text-sm text-primary font-semibold">View all</a>
        </div>
        <div class="list-group">
            @forelse ($recentBookings as $b)
                <div class="list-row">
                    <span class="list-row-ic" style="background:rgba(15,98,254,.1);color:#0F62FE"><i data-lucide="ticket" class="w-4 h-4"></i></span>
                    <div class="min-w-0 flex-1">
                        <p class="list-row-title truncate">{{ $b->title ?? $b->provider?->name }}</p>
                        <p class="list-row-sub">{{ ucfirst($b->category) }} · {{ $b->created_at->diffForHumans() }}</p>
                    </div>
                    <p class="font-semibold">{{ $cur }}{{ number_format($b->amount, 0) }}</p>
                </div>
            @empty
                <div class="p-2"><x-empty-state icon="ticket" text="No bookings yet." /></div>
            @endforelse
        </div>
    </div>
</div>
@endsection
