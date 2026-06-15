@extends('layouts.admin')
@section('title', 'Offers')
@section('heading', 'Offers & Deals')

@section('content')
<div class="flex justify-between items-center mb-5">
    <p class="text-muted text-sm">Curated cashback deals shown on the site (separate from live search).</p>
    <a href="{{ route('admin.offers.create') }}" class="btn btn-primary text-sm"><i data-lucide="plus" class="w-4 h-4"></i> New offer</a>
</div>

<div class="card overflow-hidden">
    <table class="w-full text-sm">
        <thead class="text-left text-muted border-b border-slate-100">
            <tr><th class="p-4 font-semibold">Offer</th><th class="p-4 font-semibold">Provider</th><th class="p-4 font-semibold">Category</th><th class="p-4 font-semibold">Cashback</th><th class="p-4 font-semibold">Featured</th><th class="p-4 font-semibold">Status</th><th class="p-4 font-semibold text-right">Actions</th></tr>
        </thead>
        <tbody>
            @forelse ($offers as $o)
                <tr class="border-b border-slate-50 hover:bg-slate-50">
                    <td class="p-4 font-medium">{{ $o->title }}</td>
                    <td class="p-4">{{ $o->provider?->name ?? '—' }}</td>
                    <td class="p-4">{{ ucfirst($o->category) }}</td>
                    <td class="p-4">{{ $o->cashback_label ?? ($o->cashback_type==='flat' ? '₹'.number_format($o->cashback_value,0) : $o->cashback_value.'%') }}</td>
                    <td class="p-4">@if($o->is_featured)<span class="pill pill-deal">Featured</span>@else <span class="text-muted">—</span>@endif</td>
                    <td class="p-4">
                        <form method="POST" action="{{ route('admin.offers.toggle', $o) }}">@csrf @method('PUT')
                            <button class="pill {{ $o->is_active ? 'pill-cashback' : 'pill-muted' }}">{{ $o->is_active ? 'Active' : 'Paused' }}</button>
                        </form>
                    </td>
                    <td class="p-4 text-right">
                        <a href="{{ route('admin.offers.edit', $o) }}" class="btn btn-ghost text-xs">Edit</a>
                        <form method="POST" action="{{ route('admin.offers.destroy', $o) }}" class="inline" onsubmit="return confirm('Delete offer?')">@csrf @method('DELETE')<button class="btn btn-ghost text-xs text-danger">Delete</button></form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7"><x-empty-state icon="tag" text="No offers yet. Create your first deal." /></td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4">{{ $offers->links() }}</div>
</div>
@endsection
