{{-- Horizontal/grid rail of offer cards used across the homepage. --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 py-10">
    <div class="flex items-end justify-between mb-6">
        <div>
            <h2 class="font-display text-2xl sm:text-3xl font-extrabold">{{ $title }}</h2>
            @isset($subtitle)<p class="text-muted mt-1">{{ $subtitle }}</p>@endisset
        </div>
        @isset($cta)
            <a href="{{ route('search', ['category' => $cta[0]]) }}" class="btn btn-ghost text-sm shrink-0">
                {{ $cta[1] }} <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </a>
        @endisset
    </div>
    {{-- Mobile: horizontal snap rail (app feel) · Desktop: grid --}}
    <div class="md:hidden h-scroll no-scrollbar -mx-4 px-4">
        @foreach (array_slice($offers, 0, 6) as $offer)
            <div class="w-[78%] max-w-[20rem]">
                <x-offer-card :offer="$offer" />
            </div>
        @endforeach
    </div>
    <div class="hidden md:grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
        @foreach (array_slice($offers, 0, 4) as $offer)
            <x-offer-card :offer="$offer" />
        @endforeach
    </div>
</section>
