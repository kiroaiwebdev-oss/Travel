@extends('layouts.dashboard')
@section('title', 'KYC Verification')
@section('heading', 'KYC Verification')

@section('content')
@php $st = $user->kyc_status; @endphp
<div class="max-w-2xl">
    {{-- Status banner --}}
    @if ($st === 'approved')
        <div class="card p-5 mb-5 flex items-center gap-3 border-l-4" style="border-color:#22C55E">
            <i data-lucide="badge-check" class="w-6 h-6 text-success"></i>
            <div><p class="font-semibold">KYC Approved</p><p class="text-sm text-muted">You can request withdrawals.</p></div>
        </div>
    @elseif ($st === 'pending')
        <div class="card p-5 mb-5 flex items-center gap-3 border-l-4" style="border-color:#F59E0B">
            <i data-lucide="clock" class="w-6 h-6 text-warning"></i>
            <div><p class="font-semibold">Under review</p><p class="text-sm text-muted">We're verifying your details — usually within 24–48h.</p></div>
        </div>
    @elseif ($st === 'rejected')
        <div class="card p-5 mb-5 flex items-center gap-3 border-l-4" style="border-color:#EF4444">
            <i data-lucide="x-circle" class="w-6 h-6 text-danger"></i>
            <div><p class="font-semibold">KYC Rejected</p><p class="text-sm text-muted">{{ $user->kyc_note ?? 'Please re-submit with correct details.' }}</p></div>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 rounded-xl bg-danger/10 text-danger text-sm p-3">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('dashboard.kyc.submit') }}" class="card p-6 space-y-4" x-data="{ method: '{{ $user->kyc_payout_method ?? 'upi' }}' }">
        @csrf
        <h2 class="font-display font-bold">Your details (required for payouts)</h2>
        <div>
            <label class="text-sm font-semibold">Full name (as per PAN)</label>
            <input name="kyc_full_name" value="{{ old('kyc_full_name', $user->kyc_full_name) }}" class="input mt-1" required @disabled($st==='approved')>
        </div>
        <div>
            <label class="text-sm font-semibold">PAN number</label>
            <input name="kyc_pan" value="{{ old('kyc_pan', $user->kyc_pan) }}" class="input mt-1 uppercase" placeholder="ABCDE1234F" required @disabled($st==='approved')>
        </div>
        <div>
            <label class="text-sm font-semibold">Payout method</label>
            <select name="kyc_payout_method" x-model="method" class="input mt-1" @disabled($st==='approved')>
                <option value="upi">UPI</option>
                <option value="bank">Bank account</option>
                <option value="paypal">PayPal</option>
            </select>
        </div>
        <template x-if="method === 'upi'">
            <div><label class="text-sm font-semibold">UPI ID</label><input name="payout[upi]" class="input mt-1" placeholder="name@bank" @disabled($st==='approved')></div>
        </template>
        <template x-if="method === 'bank'">
            <div class="grid sm:grid-cols-2 gap-3">
                <input name="payout[account]" class="input" placeholder="Account number" @disabled($st==='approved')>
                <input name="payout[ifsc]" class="input" placeholder="IFSC code" @disabled($st==='approved')>
                <input name="payout[name]" class="input sm:col-span-2" placeholder="Account holder name" @disabled($st==='approved')>
            </div>
        </template>
        <template x-if="method === 'paypal'">
            <div><label class="text-sm font-semibold">PayPal email</label><input name="payout[email]" class="input mt-1" placeholder="you@example.com" @disabled($st==='approved')></div>
        </template>

        @if ($st !== 'approved')
            <button class="btn btn-primary w-full justify-center">{{ $st === 'rejected' ? 'Re-submit KYC' : 'Submit KYC' }}</button>
        @endif
    </form>
</div>
@endsection
