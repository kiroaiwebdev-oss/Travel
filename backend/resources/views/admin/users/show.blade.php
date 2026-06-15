@extends('layouts.admin')
@section('title', $user->name)
@section('heading', $user->name)

@section('content')
<div class="grid lg:grid-cols-3 gap-6">
    <div class="card p-6">
        <h2 class="font-display font-bold">{{ $user->name }}</h2>
        <p class="text-sm text-muted">{{ $user->email }}</p>
        <div class="mt-4 space-y-2 text-sm">
            <div class="flex justify-between"><span class="text-muted">Wallet balance</span><span class="font-semibold">₹{{ number_format($user->wallet?->balance ?? 0, 2) }}</span></div>
            <div class="flex justify-between"><span class="text-muted">Pending</span><span>₹{{ number_format($user->wallet?->pending_balance ?? 0, 2) }}</span></div>
            <div class="flex justify-between"><span class="text-muted">Roles</span><span>{{ $user->roles->pluck('label')->join(', ') ?: 'User' }}</span></div>
            <div class="flex justify-between"><span class="text-muted">Joined</span><span>{{ $user->created_at->format('d M Y') }}</span></div>
        </div>
        <form method="POST" action="{{ route('admin.users.status', $user) }}" class="mt-5 flex gap-2">
            @csrf @method('PUT')
            <select name="status" class="input">
                @foreach (['active','suspended','banned'] as $s)<option value="{{ $s }}" @selected($user->status===$s)>{{ ucfirst($s) }}</option>@endforeach
            </select>
            <button class="btn btn-dark text-sm">Update</button>
        </form>

        @if ($errors->any())
            <div class="mt-3 rounded-xl bg-danger/10 text-danger text-xs p-2">{{ $errors->first() }}</div>
        @endif
        <div class="mt-5 pt-5 border-t border-slate-100">
            <p class="text-sm font-semibold mb-2">Manual wallet adjustment</p>
            <form method="POST" action="{{ route('admin.users.adjust', $user) }}" class="space-y-2">
                @csrf @method('PUT')
                <div class="flex gap-2">
                    <select name="direction" class="input w-28"><option value="credit">Credit +</option><option value="debit">Debit −</option></select>
                    <input type="number" step="0.01" min="0.01" name="amount" class="input" placeholder="Amount ₹" required>
                </div>
                <input name="note" class="input" placeholder="Reason (dispute / goodwill)…" required>
                <button class="btn btn-primary text-sm w-full justify-center">Apply adjustment</button>
            </form>
            <p class="text-[11px] text-muted mt-2">Recorded in the double-entry ledger &amp; audit log.</p>
        </div>
    </div>

    <div class="card overflow-hidden lg:col-span-2">
        <h2 class="font-display font-bold p-5 pb-0">Recent cashback</h2>
        <table class="w-full text-sm mt-3">
            <tbody>
                @forelse ($cashbacks as $cb)
                    <tr class="border-b border-slate-50"><td class="p-4">{{ $cb->provider?->name }}</td><td class="p-4 text-muted">{{ ucfirst($cb->category) }}</td><td class="p-4 text-right font-semibold text-success">₹{{ number_format($cb->amount, 2) }}</td><td class="p-4"><span class="pill pill-muted">{{ ucfirst($cb->status) }}</span></td></tr>
                @empty
                    <tr><td><x-empty-state icon="badge-percent" text="No cashback yet." /></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="card p-6 mt-6 max-w-3xl">
    <h2 class="font-display font-bold flex items-center gap-2"><i data-lucide="send" class="w-4 h-4"></i> Contact this user</h2>
    <p class="text-sm text-muted mt-1">Reach the user directly via Email, SMS or WhatsApp (uses configured channels).</p>
    <form method="POST" action="{{ route('admin.users.contact', $user) }}" class="mt-4 space-y-3">
        @csrf
        <div class="grid sm:grid-cols-3 gap-3">
            <div><label class="text-sm font-semibold">Channel</label>
                <select name="channel" class="input mt-1"><option value="email">Email</option><option value="sms">SMS</option><option value="whatsapp">WhatsApp</option></select>
            </div>
            <div class="sm:col-span-2"><label class="text-sm font-semibold">Subject (email)</label><input name="subject" class="input mt-1" placeholder="Optional"></div>
        </div>
        <div><label class="text-sm font-semibold">Message</label><textarea name="message" rows="3" class="input mt-1" required></textarea></div>
        <button class="btn btn-primary">Send message</button>
    </form>
</div>
@endsection
