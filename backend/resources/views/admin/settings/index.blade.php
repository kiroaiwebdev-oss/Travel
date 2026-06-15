@extends('layouts.admin')
@section('title', 'Settings')
@section('heading', 'Settings')

@section('content')
<div class="max-w-3xl space-y-6">

    {{-- ===== Branding (logo + icon) ===== --}}
    <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="card p-6">
        @csrf @method('PUT')
        <div class="flex items-center gap-2 mb-1">
            <i data-lucide="image" class="w-5 h-5 text-pay"></i>
            <h2 class="font-display font-bold">Branding</h2>
        </div>
        <p class="text-sm text-muted mb-5">Upload your platform logo &amp; icon. Changes apply everywhere instantly — header, footer, browser tab and the installable app.</p>

        <div class="grid sm:grid-cols-2 gap-6">
            {{-- Logo --}}
            <div>
                <label class="text-sm font-semibold">Logo</label>
                <p class="text-xs text-muted mb-2">Shown in the header &amp; footer. PNG / SVG / WEBP, wide format, max 2MB.</p>
                <div class="rounded-xl border border-slate-200 p-4 flex items-center justify-center bg-slate-50 h-24">
                    @if ($logo)
                        <img src="{{ $logo }}" alt="Current logo" class="max-h-14 w-auto object-contain">
                    @else
                        <span class="text-xs text-muted flex items-center gap-2"><i data-lucide="info" class="w-4 h-4"></i> Using default TripCash logo</span>
                    @endif
                </div>
                <input type="file" name="logo" accept="image/png,image/jpeg,image/webp,image/svg+xml" class="input mt-3 text-sm">
                @if ($logo)
                    <button type="submit" form="remove-logo" class="text-xs text-danger font-semibold mt-2 inline-flex items-center gap-1"><i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Remove &amp; use default</button>
                @endif
            </div>

            {{-- Icon --}}
            <div>
                <label class="text-sm font-semibold">App Icon / Favicon</label>
                <p class="text-xs text-muted mb-2">Browser tab + installed app icon. Square PNG / SVG, 512×512 recommended, max 1MB.</p>
                <div class="rounded-xl border border-slate-200 p-4 flex items-center justify-center bg-slate-50 h-24">
                    @if ($icon)
                        <img src="{{ $icon }}" alt="Current icon" class="h-14 w-14 rounded-xl object-cover">
                    @else
                        <span class="text-xs text-muted flex items-center gap-2"><i data-lucide="info" class="w-4 h-4"></i> Using default icon</span>
                    @endif
                </div>
                <input type="file" name="icon" accept="image/png,image/jpeg,image/webp,image/svg+xml,image/x-icon" class="input mt-3 text-sm">
                @if ($icon)
                    <button type="submit" form="remove-icon" class="text-xs text-danger font-semibold mt-2 inline-flex items-center gap-1"><i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Remove &amp; use default</button>
                @endif
            </div>
        </div>

        <button class="btn btn-primary mt-6"><i data-lucide="upload" class="w-4 h-4"></i> Save branding</button>
    </form>

    {{-- Hidden remove forms (separate so the main upload form stays clean) --}}
    @if ($logo)
        <form id="remove-logo" method="POST" action="{{ route('admin.settings.branding.remove') }}" class="hidden">@csrf @method('DELETE')<input type="hidden" name="key" value="site.logo"></form>
    @endif
    @if ($icon)
        <form id="remove-icon" method="POST" action="{{ route('admin.settings.branding.remove') }}" class="hidden">@csrf @method('DELETE')<input type="hidden" name="key" value="site.icon"></form>
    @endif

    {{-- ===== General key/value settings ===== --}}
    <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6">
        @csrf @method('PUT')
        @foreach ($groups as $group => $items)
            <div class="card p-6">
                <h2 class="font-display font-bold capitalize mb-4">{{ $group }}</h2>
                <div class="space-y-4">
                    @foreach ($items as $s)
                        <div class="grid sm:grid-cols-3 gap-3 items-center">
                            <label class="text-sm font-semibold">{{ ucwords(str_replace(['.','_'],' ', $s->key)) }}</label>
                            <div class="sm:col-span-2">
                                @if ($s->type === 'bool')
                                    <select name="settings[{{ $s->key }}]" class="input">
                                        <option value="1" @selected($s->typedValue())>Enabled</option>
                                        <option value="0" @selected(!$s->typedValue())>Disabled</option>
                                    </select>
                                @else
                                    <input name="settings[{{ $s->key }}]" value="{{ $s->value }}" class="input">
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
        <button class="btn btn-primary">Save settings</button>
    </form>
</div>
@endsection
