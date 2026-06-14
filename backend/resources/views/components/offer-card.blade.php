@props(['offer'])

@php
    $cur = $offer['currency'] ?? 'INR';
    $sym = $cur === 'INR' ? '₹' : ($cur === 'USD' ? '$' : $cur.' ');
    $img = $offer['images'][0] ?? null;
    $attrs = $offer['attributes'] ?? [];
@endphp

<article class="card card-hover overflow-hidden flex flex-col">
    @if ($img)
        <div class="relative aspect-[16/10] bg-slate-100">
            <img src="{{ $img }}" alt="{{ $offer['title'] }}" loading="lazy" class="w-full h-full object-cover">
            @if (($offer['cashback'] ?? 0) > 0)
                <span class="absolute top-3 left-3 pill pill-cashback bg-white/90 shadow-soft">
                    <i data-lucide="badge-percent" class="w-3.5 h-3.5"></i>
                    {{ $sym }}{{ number_format($offer['cashback']) }} cashback
                </span>
            @endif
        </div>
    @endif

    <div class="p-4 flex flex-col gap-2 flex-1">
        <div class="flex items-center gap-2">
            @if (!empty($offer['logo_url']))
                <img src="{{ $offer['logo_url'] }}" alt="" class="w-5 h-5 rounded" onerror="this.style.display='none'">
            @endif
            <span class="text-xs font-semibold text-muted">{{ $offer['provider_name'] }}</span>
            @if (!empty($offer['rating']))
                <span class="ml-auto pill pill-muted"><i data-lucide="star" class="w-3 h-3 fill-warning text-warning"></i> {{ $offer['rating'] }}
                    <span class="text-slate-400 font-normal">({{ number_format($offer['review_count'] ?? 0) }})</span></span>
            @endif
        </div>

        <h3 class="font-semibold leading-snug line-clamp-2">{{ $offer['title'] }}</h3>

        @if (!empty($attrs['airline']) || isset($offer['stops']))
            <div class="flex items-center gap-3 text-xs text-muted">
                @isset($attrs['depart_time']) <span>{{ $attrs['depart_time'] }} → {{ $attrs['arrive_time'] ?? '' }}</span> @endisset
                @isset($offer['stops']) <span>{{ $offer['stops'] == 0 ? 'Non-stop' : $offer['stops'].' stop(s)' }}</span> @endisset
                @isset($offer['duration_minutes']) <span>{{ intdiv($offer['duration_minutes'],60) }}h {{ $offer['duration_minutes']%60 }}m</span> @endisset
            </div>
        @endif

        @if (!empty($offer['amenities']))
            <div class="flex flex-wrap gap-1.5 mt-1">
                @foreach (array_slice($offer['amenities'], 0, 3) as $a)
                    <span class="pill pill-muted">{{ $a }}</span>
                @endforeach
            </div>
        @endif

        <div class="mt-auto pt-3 flex items-end justify-between border-t border-slate-100">
            <div>
                <p class="text-xs text-muted">From</p>
                <p class="text-xl font-extrabold font-display">{{ $sym }}{{ number_format($offer['price']) }}</p>
            </div>
            <a href="{{ $offer['go_url'] ?? '#' }}" rel="nofollow sponsored" target="_blank"
               class="btn btn-primary text-sm">
                Book &amp; earn <i data-lucide="arrow-up-right" class="w-4 h-4"></i>
            </a>
        </div>
    </div>
</article>
