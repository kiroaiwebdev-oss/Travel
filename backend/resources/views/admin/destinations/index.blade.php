@extends('layouts.admin')
@section('title', 'Trending Destinations')
@section('heading', 'Trending Destinations')

@section('content')
<div class="grid lg:grid-cols-3 gap-6">
    {{-- Add form --}}
    <form method="POST" action="{{ route('admin.destinations.store') }}" class="card p-6 space-y-3 self-start lg:sticky lg:top-20">
        @csrf
        <h2 class="font-display font-bold">Add destination</h2>
        <p class="text-sm text-muted">Appears in “Trending now” on the homepage.</p>
        <div><label class="text-sm font-semibold">Name</label><input name="name" class="input mt-1" required placeholder="Goa"></div>
        <div><label class="text-sm font-semibold">Tag</label><input name="tag" class="input mt-1" placeholder="Beaches"></div>
        <div><label class="text-sm font-semibold">Image URL</label><input name="image_url" class="input mt-1" required placeholder="https://images.unsplash.com/..."></div>
        <div><label class="text-sm font-semibold">Opens category</label>
            <select name="category" class="input mt-1">@foreach ($categories as $k => $c)<option value="{{ $k }}">{{ $c['label'] }}</option>@endforeach</select>
        </div>
        <div><label class="text-sm font-semibold">Sort order</label><input type="number" name="sort_order" value="0" class="input mt-1"></div>
        <button class="btn btn-primary w-full justify-center"><i data-lucide="plus" class="w-4 h-4"></i> Add destination</button>
    </form>

    {{-- List --}}
    <div class="lg:col-span-2 grid sm:grid-cols-2 gap-4">
        @forelse ($destinations as $d)
            <div class="card overflow-hidden" x-data="{ edit:false }">
                <div class="relative aspect-[16/10] bg-slate-100">
                    <img src="{{ $d->image_url }}" alt="{{ $d->name }}" class="w-full h-full object-cover" onerror="this.style.opacity=.15">
                    <span class="absolute top-2 left-2 pill {{ $d->is_active ? 'pill-cashback' : 'pill-muted' }}">{{ $d->is_active ? 'Live' : 'Hidden' }}</span>
                </div>
                <div class="p-4">
                    <div class="flex items-center justify-between gap-2">
                        <div class="min-w-0">
                            <p class="font-semibold truncate">{{ $d->name }}</p>
                            <p class="text-xs text-muted">{{ $d->tag }} · {{ ucfirst($d->category) }} · #{{ $d->sort_order }}</p>
                        </div>
                        <button @click="edit=!edit" class="btn btn-ghost text-xs shrink-0">Edit</button>
                    </div>

                    <div x-show="edit" x-collapse class="mt-3">
                        <form method="POST" action="{{ route('admin.destinations.update', $d) }}" class="space-y-2">
                            @csrf @method('PUT')
                            <input name="name" value="{{ $d->name }}" class="input text-sm" required>
                            <input name="tag" value="{{ $d->tag }}" class="input text-sm" placeholder="Tag">
                            <input name="image_url" value="{{ $d->image_url }}" class="input text-sm" required>
                            <div class="grid grid-cols-2 gap-2">
                                <select name="category" class="input text-sm">@foreach ($categories as $k => $c)<option value="{{ $k }}" @selected($d->category==$k)>{{ $c['label'] }}</option>@endforeach</select>
                                <input type="number" name="sort_order" value="{{ $d->sort_order }}" class="input text-sm">
                            </div>
                            <button class="btn btn-primary w-full justify-center text-sm">Save changes</button>
                        </form>
                    </div>

                    <div class="flex gap-2 mt-3">
                        <form method="POST" action="{{ route('admin.destinations.toggle', $d) }}" class="flex-1">@csrf @method('PUT')<button class="btn btn-ghost text-xs w-full">{{ $d->is_active ? 'Hide' : 'Show' }}</button></form>
                        <form method="POST" action="{{ route('admin.destinations.destroy', $d) }}" class="flex-1" onsubmit="return confirm('Remove this destination?')">@csrf @method('DELETE')<button class="btn btn-ghost text-xs text-danger w-full">Delete</button></form>
                    </div>
                </div>
            </div>
        @empty
            <div class="sm:col-span-2"><x-empty-state icon="map" text="No destinations yet. Add your first one." /></div>
        @endforelse
    </div>
</div>
@endsection
