@extends('layouts.admin')
@section('title', 'Dashboard')
@section('heading', 'Control Center')

@section('content')
<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
    @foreach ([
        ['Users', number_format($stats['users']), 'users'],
        ['Active providers', number_format($stats['providers']), 'plug'],
        ['Bookings', number_format($stats['bookings']), 'ticket'],
        ['GMV', '₹'.number_format($stats['gmv']), 'shopping-bag'],
        ['Commission earned', '₹'.number_format($stats['commission']), 'trending-up'],
        ['Cashback pending', '₹'.number_format($stats['cashback_pending']), 'clock'],
        ['Withdrawals to action', number_format($stats['withdrawals_pending']), 'banknote'],
        ['Searches today', number_format($stats['searches_today']), 'search'],
    ] as $card)
        <div class="card p-5">
            <div class="flex items-center justify-between">
                <span class="text-sm text-muted">{{ $card[0] }}</span>
                <i data-lucide="{{ $card[2] }}" class="w-5 h-5 text-primary"></i>
            </div>
            <p class="mt-3 text-2xl font-extrabold font-display">{{ $card[1] }}</p>
        </div>
    @endforeach
</div>

<div class="grid gap-5 lg:grid-cols-2 mt-6">
    <div class="card p-5">
        <h2 class="font-display font-bold mb-3">Recent bookings</h2>
        <table class="w-full text-sm">
            <tbody>
                @forelse ($recentBookings as $b)
                    <tr class="border-b border-slate-50">
                        <td class="py-2.5">{{ $b->user?->name ?? 'Guest' }}</td>
                        <td class="py-2.5 text-muted">{{ $b->provider?->name }}</td>
                        <td class="py-2.5 text-right font-semibold">₹{{ number_format($b->amount, 0) }}</td>
                    </tr>
                @empty
                    <tr><td><x-empty-state icon="ticket" text="No bookings yet." /></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card p-5">
        <h2 class="font-display font-bold mb-3">Pending withdrawals</h2>
        @forelse ($pendingWithdrawals as $w)
            <div class="flex items-center justify-between py-2.5 border-b border-slate-50">
                <div><p class="font-medium text-sm">{{ $w->user->name }}</p><p class="text-xs text-muted uppercase">{{ $w->method }}</p></div>
                <div class="flex items-center gap-3">
                    <span class="font-semibold">₹{{ number_format($w->amount, 2) }}</span>
                    <a href="{{ route('admin.withdrawals.index') }}" class="btn btn-ghost text-xs">Review</a>
                </div>
            </div>
        @empty
            <x-empty-state icon="check-circle" text="No withdrawals waiting." />
        @endforelse
    </div>
</div>

{{-- Communication channels + quick actions --}}
<div class="grid gap-5 lg:grid-cols-2 mt-6">
    <div class="card p-5">
        <div class="flex items-center justify-between mb-3">
            <h2 class="font-display font-bold">Communication channels</h2>
            <a href="{{ route('admin.integrations.index') }}" class="text-sm text-primary font-semibold">Configure</a>
        </div>
        <div class="space-y-2">
            @foreach (['email' => 'Email', 'sms' => 'SMS · Twilio', 'whatsapp' => 'WhatsApp'] as $k => $label)
                <div class="flex items-center justify-between py-2 border-b border-slate-50 last:border-0">
                    <span class="text-sm">{{ $label }} @if($otpChannel===$k)<span class="pill pill-brand ml-1">OTP</span>@endif</span>
                    @if (($channels[$k]['configured'] ?? false) && ($channels[$k]['enabled'] ?? false))
                        <span class="pill pill-cashback">Live</span>
                    @elseif ($channels[$k]['configured'] ?? false)
                        <span class="pill pill-muted">Off</span>
                    @else
                        <span class="pill pill-deal">Setup</span>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <div class="card p-5">
        <h2 class="font-display font-bold mb-3">Quick actions</h2>
        <div class="grid grid-cols-2 gap-2">
            <a href="{{ route('admin.offers.create') }}" class="btn btn-ghost text-sm justify-start"><i data-lucide="plus" class="w-4 h-4"></i> New offer</a>
            <a href="{{ route('admin.providers.create') }}" class="btn btn-ghost text-sm justify-start"><i data-lucide="plug" class="w-4 h-4"></i> Add provider</a>
            <a href="{{ route('admin.notifications.index') }}" class="btn btn-ghost text-sm justify-start"><i data-lucide="megaphone" class="w-4 h-4"></i> Send notification</a>
            <a href="{{ route('admin.kyc.index') }}" class="btn btn-ghost text-sm justify-start"><i data-lucide="id-card" class="w-4 h-4"></i> Review KYC</a>
            <a href="{{ route('admin.withdrawals.index') }}" class="btn btn-ghost text-sm justify-start"><i data-lucide="banknote" class="w-4 h-4"></i> Payouts</a>
            <a href="{{ route('admin.staff.index') }}" class="btn btn-ghost text-sm justify-start"><i data-lucide="user-cog" class="w-4 h-4"></i> Staff & roles</a>
        </div>
    </div>
</div>
@endsection
