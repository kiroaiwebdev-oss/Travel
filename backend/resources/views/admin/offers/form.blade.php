@extends('layouts.admin')
@section('title', $offer->exists ? 'Edit offer' : 'New offer')
@section('heading', $offer->exists ? 'Edit offer' : 'New offer')

@section('content')
@php $action = $offer->exists ? route('admin.offers.update', $offer) : route('admin.offers.store'); @endphp
<form method="POST" action="{{ $action }}" class="max-w-2xl card p-6 space-y-4">
    @csrf
    @if ($offer->exists) @method('PUT') @endif

    <div><label class="text-sm font-semibold">Title</label><input name="title" value="{{ old('title', $offer->title) }}" class="input mt-1" required></div>

    <div class="grid sm:grid-cols-2 gap-4">
        <div>
            <label class="text-sm font-semibold">Provider</label>
            <select name="provider_id" class="input mt-1"><option value="">— None —</option>
                @foreach ($providers as $p)<option value="{{ $p->id }}" @selected($offer->provider_id==$p->id)>{{ $p->name }}</option>@endforeach
            </select>
        </div>
        <div>
            <label class="text-sm font-semibold">Category</label>
            <select name="category" class="input mt-1">
                @foreach ($categories as $key => $cat)<option value="{{ $key }}" @selected($offer->category==$key)>{{ $cat['label'] }}</option>@endforeach
            </select>
        </div>
        <div><label class="text-sm font-semibold">Cashback label</label><input name="cashback_label" value="{{ old('cashback_label', $offer->cashback_label) }}" class="input mt-1" placeholder="Up to 40% cashback"></div>
        <div class="grid grid-cols-2 gap-2">
            <div><label class="text-sm font-semibold">Type</label><select name="cashback_type" class="input mt-1"><option value="percentage" @selected($offer->cashback_type==='percentage')>%</option><option value="flat" @selected($offer->cashback_type==='flat')>Flat ₹</option></select></div>
            <div><label class="text-sm font-semibold">Value</label><input type="number" step="0.01" name="cashback_value" value="{{ old('cashback_value', $offer->cashback_value ?? 0) }}" class="input mt-1"></div>
        </div>
        <div><label class="text-sm font-semibold">Image URL</label><input name="image_url" value="{{ old('image_url', $offer->image_url) }}" class="input mt-1"></div>
        <div><label class="text-sm font-semibold">Deep link</label><input name="deep_link" value="{{ old('deep_link', $offer->deep_link) }}" class="input mt-1"></div>
        <div><label class="text-sm font-semibold">Sort order</label><input type="number" name="sort_order" value="{{ old('sort_order', $offer->sort_order ?? 0) }}" class="input mt-1"></div>
        <div><label class="text-sm font-semibold">Expires at</label><input type="date" name="expires_at" value="{{ old('expires_at', optional($offer->expires_at)->format('Y-m-d')) }}" class="input mt-1"></div>
    </div>

    <div><label class="text-sm font-semibold">Description</label><textarea name="description" rows="2" class="input mt-1">{{ old('description', $offer->description) }}</textarea></div>
    <div><label class="text-sm font-semibold">Terms</label><textarea name="terms" rows="2" class="input mt-1">{{ old('terms', $offer->terms) }}</textarea></div>

    <div class="flex gap-5">
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_featured" value="1" class="rounded border-slate-300" @checked($offer->is_featured)> Featured</label>
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" value="1" class="rounded border-slate-300" @checked($offer->is_active ?? true)> Active</label>
    </div>

    <div class="flex gap-2"><button class="btn btn-primary">Save offer</button><a href="{{ route('admin.offers.index') }}" class="btn btn-ghost">Cancel</a></div>
</form>
@endsection
