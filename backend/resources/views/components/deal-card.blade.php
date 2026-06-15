@props(['offer'])

{{-- Admin-curated promotional deal card (App\Models\Offer). --}}
@php
    $cashback = $offer->cashback_label
        ?: ($offer->cashback_type === 'flat'
            ? '₹'.number_format($offer->cashback_value, 0).' cashback'
            : rtrim(rtrim(number_format($offer->cashback_value, 2), '0'), '.').'% cashback');
    $img = $offer->image_url ?: 'https://images.unsplash.com/photo-1488646953014-85cb44e25828?w=640&q=80';
    $link = $offer->deep_link ?: route('search', ['category' => $offer->category]);
@endphp

<article class="card card-hover overflow-hidden flex flex-col">
    <div class="relative aspect-[16/10] bg-slate-100 overflow-hidden">
        <img src="{{ $img }}" alt="{{ $offer->title }}" loading="lazy" class="w-full h-full object-cover transition duration-700 hover:scale-105" onerror="this.src='https://images.unsplash.com/photo-1488646953014-85cb44e25828?w=640&q=80'">
        <div class="absolute top-3 left-3">
            <span class="pill pill-cashback bg-white/95 shadow-soft"><i data-lucide="badge-percent" class="w-3.5 h-3.5"></i> {{ $cashback }}</span>
        </div>
    </div>
    <div class="p-4 flex flex-col gap-2 flex-1">
        <div class="flex items-center gap-2">
            <span class="pill pill-muted">{{ ucfirst($offer->category) }}</span>
            @if ($offer->provider)
                <span class="text-xs font-semibold text-muted">{{ $offer->provider->name }}</span>
            @endif
        </div>
        <h3 class="font-semibold leading-snug line-clamp-2">{{ $offer->title }}</h3>
        @if ($offer->description)
            <p class="text-sm text-muted line-clamp-2">{{ $offer->description }}</p>
        @endif
        <div class="mt-auto pt-3 flex items-center justify-between border-t border-slate-100">
            @if ($offer->expires_at)
                <span class="text-xs text-muted flex items-center gap-1"><i data-lucide="clock" class="w-3.5 h-3.5"></i> Ends {{ $offer->expires_at->format('d M') }}</span>
            @else
                <span class="text-xs text-muted flex items-center gap-1"><i data-lucide="sparkles" class="w-3.5 h-3.5 text-brand"></i> Limited offer</span>
            @endif
            <a href="{{ $link }}" class="btn btn-primary text-sm">Grab deal <i data-lucide="arrow-up-right" class="w-4 h-4"></i></a>
        </div>
    </div>
</article>
