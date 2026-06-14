@extends('layouts.dashboard')
@section('title', 'Wallet')
@section('heading', 'Wallet')

@section('content')
@php $cur = $wallet->currency === 'INR' ? '₹' : '$'; @endphp

<div class="card p-6 bg-gradient-to-br from-secondary to-[#10243f] text-white relative overflow-hidden">
    <div class="absolute -right-10 -top-10 w-48 h-48 rounded-full bg-primary/30 blur-3xl"></div>
    <div class="relative grid sm:grid-cols-3 gap-6">
        <div>
            <p class="text-sm text-slate-300">Withdrawable balance</p>
            <p class="text-3xl font-extrabold font-display mt-1">{{ $cur }}{{ number_format($wallet->balance, 2) }}</p>
            <a href="{{ route('dashboard.withdrawals') }}" class="btn bg-white text-secondary text-sm mt-3">Withdraw</a>
        </div>
        <div><p class="text-sm text-slate-300">Pending</p><p class="text-2xl font-bold mt-1">{{ $cur }}{{ number_format($wallet->pending_balance, 2) }}</p></div>
        <div><p class="text-sm text-slate-300">Lifetime earned</p><p class="text-2xl font-bold mt-1">{{ $cur }}{{ number_format($wallet->lifetime_earned, 2) }}</p></div>
    </div>
</div>

<div class="card mt-6 overflow-hidden">
    <h2 class="font-display font-bold p-5 pb-0">Transaction history</h2>
    <div class="overflow-x-auto">
        <table class="w-full text-sm mt-3">
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
    <div class="p-4">{{ $transactions->links() }}</div>
</div>
@endsection
