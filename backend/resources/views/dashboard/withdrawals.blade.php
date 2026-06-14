@extends('layouts.dashboard')
@section('title', 'Withdrawals')
@section('heading', 'Withdrawals')

@section('content')
@php $cur = $wallet->currency === 'INR' ? '₹' : '$'; @endphp
<div class="grid lg:grid-cols-3 gap-6">
    <div class="card p-6" x-data="{ method: 'upi' }">
        <h2 class="font-display font-bold">Request a withdrawal</h2>
        <p class="text-sm text-muted mt-1">Available: <span class="font-semibold text-success">{{ $cur }}{{ number_format($wallet->balance, 2) }}</span></p>
        @if ($errors->any())<div class="mt-3 rounded-xl bg-danger/10 text-danger text-sm p-3">{{ $errors->first() }}</div>@endif

        <form method="POST" action="{{ route('dashboard.withdrawals.store') }}" class="space-y-4 mt-4">
            @csrf
            <div>
                <label class="text-sm font-semibold">Amount (min {{ $cur }}{{ number_format($min, 0) }})</label>
                <input type="number" name="amount" min="{{ $min }}" step="0.01" class="input mt-1" required>
            </div>
            <div>
                <label class="text-sm font-semibold">Method</label>
                <select name="method" x-model="method" class="input mt-1">
                    <option value="upi">UPI</option><option value="bank">Bank transfer</option>
                    <option value="paypal">PayPal</option><option value="voucher">Gift voucher</option>
                </select>
            </div>
            <template x-if="method === 'upi'">
                <div><label class="text-sm font-semibold">UPI ID</label><input name="payout_details[upi]" class="input mt-1" placeholder="name@bank"></div>
            </template>
            <template x-if="method === 'bank'">
                <div class="space-y-2">
                    <input name="payout_details[account]" class="input" placeholder="Account number">
                    <input name="payout_details[ifsc]" class="input" placeholder="IFSC">
                </div>
            </template>
            <template x-if="method === 'paypal'">
                <div><input name="payout_details[email]" class="input" placeholder="PayPal email"></div>
            </template>
            <button class="btn btn-primary w-full justify-center">Request withdrawal</button>
        </form>
    </div>

    <div class="card overflow-hidden lg:col-span-2">
        <h2 class="font-display font-bold p-5 pb-0">History</h2>
        <table class="w-full text-sm mt-3">
            <thead class="text-left text-muted border-b border-slate-100"><tr><th class="p-4 font-semibold">Date</th><th class="p-4 font-semibold">Method</th><th class="p-4 font-semibold text-right">Amount</th><th class="p-4 font-semibold">Status</th></tr></thead>
            <tbody>
                @forelse ($withdrawals as $w)
                    <tr class="border-b border-slate-50"><td class="p-4 text-muted">{{ $w->created_at->format('d M Y') }}</td><td class="p-4 uppercase">{{ $w->method }}</td><td class="p-4 text-right font-semibold">{{ $cur }}{{ number_format($w->amount, 2) }}</td><td class="p-4"><span class="pill pill-muted">{{ ucfirst($w->status) }}</span></td></tr>
                @empty
                    <tr><td colspan="4"><x-empty-state icon="banknote" text="No withdrawals yet." /></td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4">{{ $withdrawals->links() }}</div>
    </div>
</div>
@endsection
