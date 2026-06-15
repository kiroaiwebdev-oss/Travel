@extends('layouts.dashboard')
@section('title', 'Referrals')
@section('heading', 'Referrals')

@section('content')
<div class="card p-6" x-data="{ copied:false }">
    <div class="grid sm:grid-cols-3 gap-6 items-center">
        <div class="sm:col-span-2">
            <h2 class="font-display font-bold text-lg">Invite friends, earn ₹{{ number_format((float) config('tripcash.referral.reward_amount'), 0) }} each</h2>
            <p class="text-muted text-sm mt-1">Share your link. You earn once they make their first confirmed booking.</p>
            <div class="flex gap-2 mt-4">
                <input readonly value="{{ $link }}" class="input" x-ref="link">
                <button @click="navigator.clipboard.writeText($refs.link.value); copied=true; setTimeout(()=>copied=false,1500)" class="btn btn-dark text-sm">
                    <span x-text="copied ? 'Copied!' : 'Copy'"></span>
                </button>
            </div>
        </div>
        <div class="card p-5 text-center bg-bg">
            <p class="text-sm text-muted">Total earned</p>
            <p class="text-3xl font-extrabold font-display text-success mt-1">₹{{ number_format($earned, 0) }}</p>
            <p class="text-xs text-muted mt-1">Code: <span class="font-mono font-semibold">{{ $code }}</span></p>
        </div>
    </div>
</div>

<div class="card mt-6 overflow-hidden">
    <h2 class="font-display font-bold p-5 pb-0">Your referrals</h2>
    <table class="w-full text-sm mt-3">
        <thead class="text-left text-muted border-b border-slate-100"><tr><th class="p-4 font-semibold">Friend</th><th class="p-4 font-semibold">Status</th><th class="p-4 font-semibold text-right">Reward</th><th class="p-4 font-semibold">Joined</th></tr></thead>
        <tbody>
            @forelse ($referrals as $r)
                <tr class="border-b border-slate-50">
                    <td class="p-4">{{ $r->referee?->name ?? 'Pending signup' }}</td>
                    <td class="p-4"><span class="pill pill-muted">{{ ucfirst($r->status) }}</span></td>
                    <td class="p-4 text-right font-semibold">₹{{ number_format($r->reward_amount, 0) }}</td>
                    <td class="p-4 text-muted">{{ $r->created_at->format('d M Y') }}</td>
                </tr>
            @empty
                <tr><td colspan="4"><x-empty-state icon="users" text="No referrals yet. Share your link to start earning." /></td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4">{{ $referrals->links() }}</div>
</div>
@endsection
