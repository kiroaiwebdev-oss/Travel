@extends('layouts.admin')
@section('title', $rule->exists ? 'Edit rule' : 'New rule')
@section('heading', $rule->exists ? 'Edit cashback rule' : 'New cashback rule')

@section('content')
@php $action = $rule->exists ? route('admin.cashback-rules.update', $rule) : route('admin.cashback-rules.store'); @endphp
<form method="POST" action="{{ $action }}" class="max-w-2xl card p-6 space-y-4">
    @csrf
    @if ($rule->exists) @method('PUT') @endif

    <div><label class="text-sm font-semibold">Rule name</label><input name="name" value="{{ old('name', $rule->name) }}" class="input mt-1" required></div>

    <div class="grid sm:grid-cols-2 gap-4">
        <div>
            <label class="text-sm font-semibold">Provider</label>
            <select name="provider_id" class="input mt-1"><option value="">Any provider</option>
                @foreach ($providers as $p)<option value="{{ $p->id }}" @selected($rule->provider_id==$p->id)>{{ $p->name }}</option>@endforeach
            </select>
        </div>
        <div>
            <label class="text-sm font-semibold">Category</label>
            <select name="category" class="input mt-1"><option value="">Any category</option>
                @foreach ($categories as $key => $cat)<option value="{{ $key }}" @selected($rule->category==$key)>{{ $cat['label'] }}</option>@endforeach
            </select>
        </div>
        <div>
            <label class="text-sm font-semibold">Type</label>
            <select name="type" class="input mt-1">
                <option value="percentage" @selected($rule->type==='percentage')>Percentage (share of commission)</option>
                <option value="fixed" @selected($rule->type==='fixed')>Fixed amount</option>
            </select>
        </div>
        <div><label class="text-sm font-semibold">Value</label><input type="number" step="0.01" name="value" value="{{ old('value', $rule->value) }}" class="input mt-1" required></div>
        <div><label class="text-sm font-semibold">Max cap (₹)</label><input type="number" step="0.01" name="max_cap" value="{{ old('max_cap', $rule->max_cap) }}" class="input mt-1"></div>
        <div><label class="text-sm font-semibold">Min booking (₹)</label><input type="number" step="0.01" name="min_booking_amount" value="{{ old('min_booking_amount', $rule->min_booking_amount ?? 0) }}" class="input mt-1"></div>
        <div><label class="text-sm font-semibold">Priority (lower wins)</label><input type="number" name="priority" value="{{ old('priority', $rule->priority ?? 100) }}" class="input mt-1" required></div>
        <div class="flex items-end"><label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" value="1" class="rounded border-slate-300" @checked($rule->is_active ?? true)> Active</label></div>
    </div>

    <div class="flex gap-2"><button class="btn btn-primary">Save rule</button><a href="{{ route('admin.cashback-rules.index') }}" class="btn btn-ghost">Cancel</a></div>
</form>
@endsection
