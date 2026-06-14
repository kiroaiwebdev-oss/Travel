@extends('layouts.app')

@section('title', ucfirst($query->category).' results — '.config('app.name'))

@section('content')
<div class="bg-white border-b border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-5">
        <x-search-widget :categories="$categories" :active="$query->category" />
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 py-8"
     x-data="{ sort: '{{ $query->sort }}' }">
    <div class="flex flex-col lg:flex-row gap-6">

        {{-- ===== FILTERS ===== --}}
        <aside class="lg:w-72 shrink-0">
            <form method="GET" action="{{ route('search') }}" class="card p-5 space-y-5 lg:sticky lg:top-20">
                <input type="hidden" name="category" value="{{ $query->category }}">
                <input type="hidden" name="destination" value="{{ $query->destination }}">
                <input type="hidden" name="origin" value="{{ $query->origin }}">

                <div class="flex items-center justify-between">
                    <h3 class="font-display font-bold">Filters</h3>
                    <a href="{{ route('search', ['category' => $query->category]) }}" class="text-xs text-primary font-semibold">Clear</a>
                </div>

                <div>
                    <label class="text-sm font-semibold">Max price</label>
                    <input type="number" name="filters[price_max]" class="input mt-1" placeholder="e.g. 8000" value="{{ request('filters.price_max') }}">
                </div>

                <div>
                    <label class="text-sm font-semibold">Min rating</label>
                    <select name="filters[rating]" class="input mt-1">
                        <option value="">Any</option>
                        @foreach ([4.5, 4, 3.5, 3] as $r)
                            <option value="{{ $r }}" @selected(request('filters.rating') == $r)>{{ $r }}+</option>
                        @endforeach
                    </select>
                </div>

                @if ($query->category === 'flights')
                    <div>
                        <label class="text-sm font-semibold">Max stops</label>
                        <select name="filters[max_stops]" class="input mt-1">
                            <option value="">Any</option>
                            <option value="0" @selected(request('filters.max_stops')==='0')>Non-stop</option>
                            <option value="1" @selected(request('filters.max_stops')==='1')>1 stop</option>
                        </select>
                    </div>
                @endif

                <input type="hidden" name="sort" :value="sort">
                <button class="btn btn-primary w-full justify-center">Apply filters</button>
            </form>
        </aside>

        {{-- ===== RESULTS ===== --}}
        <div class="flex-1 min-w-0">
            <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
                <div>
                    <h1 class="font-display text-xl font-extrabold">
                        {{ $meta['count'] }} {{ $categories[$query->category]['label'] ?? 'results' }}
                        @if ($query->destination) <span class="text-muted font-normal">in {{ $query->destination }}</span> @endif
                    </h1>
                    <p class="text-xs text-muted mt-0.5">
                        Searched in {{ $meta['response_ms'] }}ms · {{ $meta['cache_hit'] ? 'cached' : 'live' }}
                        @if (($meta['best_cashback'] ?? 0) > 0) · up to ₹{{ number_format($meta['best_cashback']) }} cashback @endif
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <span class="text-sm text-muted">Sort</span>
                    <select x-model="sort" class="input py-2 w-44" data-sort-select>
                        <option value="best_value">Best value</option>
                        <option value="lowest_price">Lowest price</option>
                        <option value="highest_cashback">Highest cashback</option>
                        <option value="highest_rating">Highest rating</option>
                    </select>
                </div>
            </div>

            @if (count($offers))
                <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach ($offers as $offer)
                        <x-offer-card :offer="$offer" />
                    @endforeach
                </div>
            @else
                <div class="card p-12 text-center">
                    <div class="mx-auto w-14 h-14 grid place-items-center rounded-2xl bg-slate-100 text-muted mb-4">
                        <i data-lucide="search-x" class="w-7 h-7"></i>
                    </div>
                    <h3 class="font-display font-bold text-lg">No results found</h3>
                    <p class="text-muted mt-1">Try adjusting your filters or searching a different destination.</p>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Preserve current query while changing sort.
    function updateSort(sort) {
        const u = new URL(window.location.href);
        u.searchParams.set('sort', sort);
        return u.toString();
    }
    document.querySelectorAll('[data-sort-select]').forEach(el => {
        el.addEventListener('change', () => window.location.assign(updateSort(el.value)));
    });
</script>
@endpush
@endsection
