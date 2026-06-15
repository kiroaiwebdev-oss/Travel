@extends('layouts.app')
@section('title', 'Trending destinations — '.config('app.name'))

@section('content')
<section class="hero-aurora border-b border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 pt-8 pb-7 text-center">
        <span class="pill pill-brand mx-auto"><i data-lucide="trending-up" class="w-3.5 h-3.5"></i> Trending now</span>
        <h1 class="mt-3 font-display text-3xl sm:text-4xl font-extrabold">Popular destinations</h1>
        <p class="text-muted mt-2 max-w-xl mx-auto">Tap a destination to find cashback hotels, flights &amp; packages — book as usual and earn real money back.</p>
    </div>
</section>

<section class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
        @foreach ($destinations as $d)
            <a href="{{ route('search', ['category' => $d->category ?? 'hotels', 'destination' => $d->name]) }}"
               class="group relative aspect-[3/4] rounded-xl2 overflow-hidden card-hover">
                <img src="{{ $d->image_url }}" alt="{{ $d->name }}" loading="lazy"
                     class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition duration-700"
                     onerror="this.style.opacity=.2">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                @if ($d->tag)
                    <span class="absolute top-3 left-3 pill bg-white/90 text-ink text-[11px]">{{ $d->tag }}</span>
                @endif
                <div class="absolute bottom-3 left-3 right-3 text-white">
                    <p class="font-bold text-lg leading-tight">{{ $d->name }}</p>
                    <span class="mt-1 inline-flex items-center gap-1 text-xs font-semibold text-white/90 opacity-0 group-hover:opacity-100 transition">
                        Explore deals <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                    </span>
                </div>
            </a>
        @endforeach
    </div>

    <div class="mt-10 text-center">
        <a href="{{ route('search', ['category' => 'hotels']) }}" class="btn btn-primary">
            Search all destinations <i data-lucide="search" class="w-4 h-4"></i>
        </a>
    </div>
</section>
@endsection
