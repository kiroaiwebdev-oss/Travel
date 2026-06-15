@extends('layouts.dashboard')
@section('title', 'Wallet')
@section('heading', 'Wallet')

@section('content')
@php $cur = $wallet->currency === 'INR' ? '₹' : '$'; @endphp

{{-- ===== Balance hero ===== --}}
<div class="app-balance p-6">
    <div class="relative">
        <div class="lg:grid lg:grid-cols-3 lg:gap-6">
            <div class="lg:col-span-1">
                <p class="text-sm text-white/70">Withdrawable balance</p>
                <p class="text-4xl font-extrabold font-display mt-1">{{ $cur }}{{ number_format($wallet->balance, 2) }}</p>
                <a href="{{ route('dashboard.withdrawals') }}" class="press inline-flex items-center gap-2 rounded-xl bg-white text-ink font-bold text-sm px-5 py-2.5 mt-4">
                    <i data-lucide="banknote" class="w-4 h-4"></i> Withdraw
                </a>
            </div>
            <div class="grid grid-cols-2 gap-3 mt-5 lg:mt-0 lg:col-span-2">
                <div class="rounded-xl p-4" style="background:rgba(255,255,255,.08)">
                    <p class="text-xs text-white/60">Pending</p>
                    <p class="text-xl font-bold mt-1">{{ $cur }}{{ number_format($wallet->pending_balance, 2) }}</p>
                </div>
                <div class="rounded-xl p-4" style="background:rgba(255,255,255,.08)">
                    <p class="text-xs text-white/60">Lifetime earned</p>
                    <p class="text-xl font-bold mt-1">{{ $cur }}{{ number_format($wallet->lifetime_earned, 2) }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ===== Transactions ===== --}}
<div class="mt-6">
    <div class="flex items-center justify-between mb-2 px-1">
        <h2 class="font-display font-bold">Transaction history</h2>
    </div>

    {{-- Mobile: app list rows --}}
    <div class="lg:hidden list-group">
        @forelse ($transactions as $t)
            @php $credit = $t->direction === 'credit'; @endphp
            <div class="list-row">
                <span class="list-row-ic" style="{{ $credit ? 'background:rgba(34,197,94,.1);color:#16a34a' : 'background:rgba(239,68,68,.1);color:#dc2626' }}">
                    <i data-lucide="{{ $credit ? 'arrow-down-left' : 'arrow-up-right' }}" class="w-4 h-4"></i>
                </span>
                <div class="min-w-0 flex-1">
                    <p class="list-row-title truncate">{{ $t->description ?: ucfirst(str_replace('_',' ',$t->type)) }}</p>
                    <p class="list-row-sub">{{ $t->created_at->format('d M Y') }} · {{ str_replace('_',' ',$t->type) }}</p>
                </div>
                <div class="text-right">
                    <p class="font-semibold {{ $credit ? 'text-success' : 'text-danger' }}">{{ $credit ? '+' : '−' }}{{ $cur }}{{ number_format($t->amount, 2) }}</p>
                    <p class="text-[11px] text-muted">Bal {{ $cur }}{{ number_format($t->balance_after, 2) }}</p>
                </div>
            </div>
        @empty
            <div class="p-2"><x-empty-state icon="receipt" text="No transactions yet." /></div>
        @endforelse
    </div>

    {{-- Desktop: table --}}
    <div class="hidden lg:block card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-left text-muted border-b border-slate-100">
                    <tr><th class="p-4 font-semibold">Date</th><th class="p-4 font-semibold">Type</th><th class="p-4 font-semibold">Description</th><th class="p-4 font-semibold text-right">Amount</th><th class="p-4 font-semibold text-right">Balance</th></tr>
                </thead>
                <tbody>
                    @forelse ($transactions as $t)
                        <tr class="border-b border-slate-50 hover:bg-slate-50">
                            <td class="p-4 text-muted">{{ $t->created_at->format('d M Y') }}</td>
                            <td class="p-4"><span class="pill pill-muted">{{ str_replace('_',' ',$t->type) }}</span></td>
                            <td class="p-4">{{ $t->description }}</td>
                            <td class="p-4 text-right font-semibold {{ $t->direction === 'credit' ? 'text-success' : 'text-danger' }}">
                                {{ $t->direction === 'credit' ? '+' : '−' }}{{ $cur }}{{ number_format($t->amount, 2) }}
                            </td>
                            <td class="p-4 text-right text-muted">{{ $cur }}{{ number_format($t->balance_after, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5"><x-empty-state icon="receipt" text="No transactions yet." /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $transactions->links() }}</div>
</div>
@endsection
