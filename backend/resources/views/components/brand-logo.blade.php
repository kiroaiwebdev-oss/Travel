@props(['light' => false, 'icon' => 'plane', 'compact' => false])

{{-- Platform logo. If an admin has uploaded a custom logo (site.logo setting),
     it renders that image everywhere; otherwise the default TripCash wordmark. --}}
@php $brandLogo = \App\Models\Setting::get('site.logo'); @endphp

@if ($brandLogo)
    <img src="{{ $brandLogo }}" alt="{{ config('app.name', 'TripCash') }}"
         class="{{ $compact ? 'h-7' : 'h-9' }} w-auto object-contain" loading="eager">
@else
    <span class="flex items-center gap-2 font-display font-extrabold {{ $compact ? 'text-base' : 'text-lg' }} {{ $light ? 'text-white' : '' }}">
        <span class="grid place-items-center {{ $compact ? 'w-8 h-8' : 'w-9 h-9' }} rounded-xl text-white shadow-lift" style="background:linear-gradient(150deg,#14b8a6,#0d9488)">
            <i data-lucide="{{ $icon }}" class="{{ $compact ? 'w-4 h-4' : 'w-5 h-5' }}"></i>
        </span>
        <span>Trip<span class="{{ $light ? '' : 'text-brand' }}" @if($light) style="color:#2dd4cb" @endif>Cash</span></span>
    </span>
@endif
