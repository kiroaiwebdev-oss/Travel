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
@endsection
