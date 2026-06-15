@extends('layouts.admin')
@section('title', 'Providers')
@section('heading', 'Providers')

@section('content')
<div class="flex justify-between items-center mb-5">
    <p class="text-muted text-sm">Add a provider, drop in API keys, and it goes live instantly across search.</p>
    <a href="{{ route('admin.providers.create') }}" class="btn btn-primary text-sm"><i data-lucide="plus" class="w-4 h-4"></i> Add provider</a>
</div>

<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="text-left text-muted border-b border-slate-100">
                <tr><th class="p-4 font-semibold">Provider</th><th class="p-4 font-semibold">Categories</th><th class="p-4 font-semibold">Adapter</th><th class="p-4 font-semibold">Commission</th><th class="p-4 font-semibold">Mode</th><th class="p-4 font-semibold">Status</th><th class="p-4 font-semibold text-right">Actions</th></tr>
            </thead>
            <tbody>
                @forelse ($providers as $p)
                    <tr class="border-b border-slate-50 hover:bg-slate-50">
                        <td class="p-4 font-medium flex items-center gap-2">
                            @if ($p->logo_url)<img src="{{ $p->logo_url }}" class="w-5 h-5 rounded" onerror="this.style.display='none'">@endif {{ $p->name }}
                        </td>
                        <td class="p-4"><div class="flex flex-wrap gap-1">@foreach ($p->categories as $c)<span class="pill pill-muted">{{ $c }}</span>@endforeach</div></td>
                        <td class="p-4"><span class="font-mono text-xs">{{ $p->adapter }}</span></td>
                        <td class="p-4">{{ $p->commission_percent }}%</td>
                        <td class="p-4">
                            @if ($p->isDemoMode())
                                <span class="pill pill-muted">Demo</span>
                            @else
                                <span class="pill pill-cashback">Live</span>
                            @endif
                        </td>
                        <td class="p-4">
                            <form method="POST" action="{{ route('admin.providers.toggle', $p) }}">@csrf @method('PUT')
                                <button class="pill {{ $p->is_active ? 'pill-cashback' : 'pill-muted' }}">{{ $p->is_active ? 'Active' : 'Paused' }}</button>
                            </form>
                        </td>
                        <td class="p-4 text-right">
                            <a href="{{ route('admin.providers.edit', $p) }}" class="btn btn-ghost text-xs">Edit</a>
                            <form method="POST" action="{{ route('admin.providers.destroy', $p) }}" class="inline" onsubmit="return confirm('Remove this provider?')">@csrf @method('DELETE')
                                <button class="btn btn-ghost text-xs text-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7"><x-empty-state icon="plug" text="No providers yet. Add your first one." /></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4">{{ $providers->links() }}</div>
</div>
@endsection
