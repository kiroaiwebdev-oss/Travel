@extends('layouts.admin')
@section('title', $provider->exists ? 'Edit provider' : 'Add provider')
@section('heading', $provider->exists ? 'Edit '.$provider->name : 'Add provider')

@section('content')
@php
    $cfg = optional($provider->activeConfiguration)->config ?? [];
    $action = $provider->exists ? route('admin.providers.update', $provider) : route('admin.providers.store');
@endphp
<form method="POST" action="{{ $action }}" class="max-w-3xl space-y-6">
    @csrf
    @if ($provider->exists) @method('PUT') @endif

    <div class="card p-6 space-y-4">
        <h2 class="font-display font-bold">Provider details</h2>
        <div class="grid sm:grid-cols-2 gap-4">
            <div><label class="text-sm font-semibold">Name</label><input name="name" value="{{ old('name', $provider->name) }}" class="input mt-1" required></div>
            <div>
                <label class="text-sm font-semibold">Affiliate network</label>
                <select name="affiliate_network_id" class="input mt-1">
                    <option value="">— None —</option>
                    @foreach ($networks as $n)<option value="{{ $n->id }}" @selected($provider->affiliate_network_id==$n->id)>{{ $n->name }}</option>@endforeach
                </select>
            </div>
            <div>
                <label class="text-sm font-semibold">Adapter (driver)</label>
                <select name="adapter" class="input mt-1">
                    @foreach ($drivers as $d)<option value="{{ $d }}" @selected($provider->adapter==$d)>{{ $d }}</option>@endforeach
                </select>
            </div>
            <div><label class="text-sm font-semibold">Commission %</label><input type="number" step="0.01" name="commission_percent" value="{{ old('commission_percent', $provider->commission_percent ?? 5) }}" class="input mt-1" required></div>
            <div><label class="text-sm font-semibold">Priority (lower = first)</label><input type="number" name="priority" value="{{ old('priority', $provider->priority ?? 100) }}" class="input mt-1" required></div>
            <div><label class="text-sm font-semibold">Logo URL</label><input name="logo_url" value="{{ old('logo_url', $provider->logo_url) }}" class="input mt-1"></div>
        </div>
        <div>
            <label class="text-sm font-semibold">Categories</label>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 mt-2">
                @foreach ($categories as $key => $cat)
                    <label class="flex items-center gap-2 text-sm card p-2.5 cursor-pointer">
                        <input type="checkbox" name="categories[]" value="{{ $key }}" class="rounded border-slate-300" @checked(in_array($key, $provider->categories ?? []))>
                        {{ $cat['label'] }}
                    </label>
                @endforeach
            </div>
        </div>
        <div><label class="text-sm font-semibold">Tracking template</label>
            <input name="tracking_template" value="{{ old('tracking_template', $provider->tracking_template) }}" class="input mt-1 font-mono text-xs" placeholder="https://{host}/deeplink?subid={click_id}&url={target}">
            <p class="text-xs text-muted mt-1">Placeholders: <code>{host}</code> <code>{click_id}</code> <code>{target}</code> <code>{offer_ref}</code></p>
        </div>
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" value="1" class="rounded border-slate-300" @checked($provider->is_active ?? true)> Active</label>
    </div>

    <div class="card p-6 space-y-4">
        <h2 class="font-display font-bold">API credentials <span class="pill pill-muted ml-1">encrypted at rest</span></h2>
        <p class="text-sm text-muted">Leave Base URL empty to keep the provider in demo mode (sample offers). Add a Base URL + key to go live instantly.</p>
        <div class="grid sm:grid-cols-2 gap-4">
            <div><label class="text-sm font-semibold">Base URL</label><input name="config[base_url]" value="{{ old('config.base_url', $cfg['base_url'] ?? '') }}" class="input mt-1" placeholder="https://api.provider.com"></div>
            <div><label class="text-sm font-semibold">Search path</label><input name="config[search_path]" value="{{ old('config.search_path', $cfg['search_path'] ?? '/search') }}" class="input mt-1"></div>
            <div><label class="text-sm font-semibold">API key</label><input name="config[api_key]" type="password" class="input mt-1" placeholder="{{ !empty($cfg['api_key']) ? '•••••• (set)' : '' }}"></div>
            <div><label class="text-sm font-semibold">Secret key</label><input name="config[secret_key]" type="password" class="input mt-1" placeholder="{{ !empty($cfg['secret_key']) ? '•••••• (set)' : '' }}"></div>
            <div><label class="text-sm font-semibold">Host</label><input name="config[host]" value="{{ old('config.host', $cfg['host'] ?? '') }}" class="input mt-1" placeholder="provider.com"></div>
        </div>
    </div>

    <div class="flex gap-2">
        <button class="btn btn-primary">Save provider</button>
        <a href="{{ route('admin.providers.index') }}" class="btn btn-ghost">Cancel</a>
    </div>
</form>
@endsection
