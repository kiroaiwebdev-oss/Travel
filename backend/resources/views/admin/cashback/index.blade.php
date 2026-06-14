@extends('layouts.admin')
@section('title', 'Cashback rules')
@section('heading', 'Cashback rules')

@section('content')
<div class="flex justify-between items-center mb-5">
    <p class="text-muted text-sm">Most specific rule wins: provider+category &gt; provider &gt; category &gt; global.</p>
    <a href="{{ route('admin.cashback-rules.create') }}" class="btn btn-primary text-sm"><i data-lucide="plus" class="w-4 h-4"></i> New rule</a>
</div>

<div class="card overflow-hidden">
    <table class="w-full text-sm">
        <thead class="text-left text-muted border-b border-slate-100">
            <tr><th class="p-4 font-semibold">Name</th><th class="p-4 font-semibold">Scope</th><th class="p-4 font-semibold">Type</th><th class="p-4 font-semibold">Value</th><th class="p-4 font-semibold">Priority</th><th class="p-4 font-semibold">Status</th><th class="p-4 font-semibold text-right">Actions</th></tr>
        </thead>
        <tbody>
            @forelse ($rules as $r)
                <tr class="border-b border-slate-50 hover:bg-slate-50">
                    <td class="p-4 font-medium">{{ $r->name }}</td>
                    <td class="p-4 text-muted">{{ $r->provider?->name ?? 'Any provider' }} / {{ $r->category ?? 'any category' }}</td>
                    <td class="p-4">{{ ucfirst($r->type) }}</td>
                    <td class="p-4 font-semibold">{{ $r->type === 'fixed' ? '₹'.number_format($r->value,0) : $r->value.'%' }}</td>
                    <td class="p-4">{{ $r->priority }}</td>
                    <td class="p-4"><span class="pill {{ $r->is_active ? 'pill-cashback' : 'pill-muted' }}">{{ $r->is_active ? 'Active' : 'Off' }}</span></td>
                    <td class="p-4 text-right">
                        <a href="{{ route('admin.cashback-rules.edit', $r) }}" class="btn btn-ghost text-xs">Edit</a>
                        <form method="POST" action="{{ route('admin.cashback-rules.destroy', $r) }}" class="inline" onsubmit="return confirm('Delete rule?')">@csrf @method('DELETE')<button class="btn btn-ghost text-xs text-danger">Delete</button></form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7"><x-empty-state icon="badge-percent" text="No cashback rules yet." /></td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4">{{ $rules->links() }}</div>
</div>
@endsection
