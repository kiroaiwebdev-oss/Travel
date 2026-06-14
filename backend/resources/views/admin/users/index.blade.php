@extends('layouts.admin')
@section('title', 'Users')
@section('heading', 'Users')

@section('content')
<form method="GET" class="mb-5 flex gap-2 max-w-md">
    <input name="q" value="{{ $q }}" class="input" placeholder="Search name or email…">
    <button class="btn btn-dark">Search</button>
</form>

<div class="card overflow-hidden">
    <table class="w-full text-sm">
        <thead class="text-left text-muted border-b border-slate-100"><tr><th class="p-4 font-semibold">User</th><th class="p-4 font-semibold">Wallet</th><th class="p-4 font-semibold">Status</th><th class="p-4 font-semibold">Joined</th><th class="p-4 font-semibold text-right"></th></tr></thead>
        <tbody>
            @forelse ($users as $u)
                <tr class="border-b border-slate-50 hover:bg-slate-50">
                    <td class="p-4"><p class="font-medium">{{ $u->name }}</p><p class="text-xs text-muted">{{ $u->email }}</p></td>
                    <td class="p-4 font-semibold">₹{{ number_format($u->wallet?->balance ?? 0, 2) }}</td>
                    <td class="p-4"><span class="pill {{ $u->status==='active' ? 'pill-cashback' : 'pill-muted' }}">{{ ucfirst($u->status) }}</span></td>
                    <td class="p-4 text-muted">{{ $u->created_at->format('d M Y') }}</td>
                    <td class="p-4 text-right"><a href="{{ route('admin.users.show', $u) }}" class="btn btn-ghost text-xs">View</a></td>
                </tr>
            @empty
                <tr><td colspan="5"><x-empty-state icon="users" text="No users found." /></td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4">{{ $users->links() }}</div>
</div>
@endsection
