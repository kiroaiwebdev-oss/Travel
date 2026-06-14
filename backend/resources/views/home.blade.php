@extends('layouts.app')

@section('content')
{{-- ===== HERO ===== --}}
<section class="relative hero-aurora">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 pt-16 pb-10 sm:pt-24 sm:pb-16">
        <div class="max-w-3xl mx-auto text-center fade-up">
            <span class="pill pill-cashback mx-auto"><i data-lucide="sparkles" class="w-3.5 h-3.5"></i> Up to {{ (int) \App\Models\Setting::get('cashback.default_share_percent', 40) }}% of our commission back to you</span>
            <h1 class="mt-5 font-display text-4xl sm:text-6xl font-extrabold tracking-tight leading-[1.05]">
                Travel more.<br>Earn <span class="text-gradient">real cashback</span> on every trip.
            </h1>
            <p class="mt-5 text-lg text-muted max-w-xl mx-auto">
                Compare flights, hotels, trains, cabs &amp; packages across all top providers — and get paid back into your wallet for booking.
            </p>
        </div>

        <div class="mt-10 max-w-5xl mx-auto fade-up">
            <x-search-widget :categories="$categories" active="hotels" />
        </div>

        {{-- Trust counters --}}
        <div class="mt-10 grid grid-cols-2 sm:grid-cols-4 gap-4 max-w-4xl mx-auto"
             x-data="{ stats: [{n:1200000,l:'Paid as cashback',p:'₹'},{n:9,l:'Providers compared',s:'+'},{n:50000,l:'Happy travellers',s:'+'},{n:4.8,l:'Average rating',d:1}] }">
            <template x-for="s in stats">
                <div class="card p-4 text-center">
                    <p class="text-2xl font-extrabold font-display counter"
                       x-data="{v:0}" x-intersect.once="let t=s.n,step=t/40,i=setInterval(()=>{v+=step;if(v>=t){v=t;clearInterval(i)};$el.textContent=(s.p||'')+(s.d?v.toFixed(1):Math.floor(v).toLocaleString())+(s.s||'')},25)">0</p>
                    <p class="text-xs text-muted mt-1" x-text="s.l"></p>
                </div>
            </template>
        </div>
    </div>
</section>

{{-- ===== POPULAR DESTINATIONS ===== --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 py-14">
    <div class="flex items-end justify-between mb-6">
        <div>
            <h2 class="font-display text-2xl sm:text-3xl font-extrabold">Popular destinations</h2>
            <p class="text-muted mt-1">Trending right now with the best cashback.</p>
        </div>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        @foreach ($destinations as $d)
            <a href="{{ route('search', ['category' => 'hotels', 'destination' => $d['name']]) }}"
               class="group relative aspect-[3/4] rounded-xl2 overflow-hidden card-hover">
                <img src="{{ $d['image'] }}" alt="{{ $d['name'] }}" loading="lazy" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-500">
                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/10 to-transparent"></div>
                <div class="absolute bottom-3 left-3 text-white">
                    <p class="text-xs opacity-80">{{ $d['tag'] }}</p>
                    <p class="font-semibold">{{ $d['name'] }}</p>
                </div>
            </a>
        @endforeach
    </div>
</section>

{{-- ===== FEATURED HOTELS ===== --}}
@includeWhen(!empty($featured['hotels']), 'partials.offer-rail', ['title' => 'Featured hotels', 'subtitle' => 'Hand-picked stays with boosted cashback.', 'offers' => $featured['hotels'] ?? [], 'cta' => ['hotels', 'View all hotels']])

{{-- ===== CASHBACK EXPLAINER ===== --}}
<section id="cashback" class="max-w-7xl mx-auto px-4 sm:px-6 py-16">
    <div class="card p-8 sm:p-12 bg-gradient-to-br from-secondary to-[#10243f] text-white relative overflow-hidden">
        <div class="absolute -right-16 -top-16 w-72 h-72 rounded-full bg-primary/30 blur-3xl"></div>
        <div class="relative">
            <h2 class="font-display text-3xl font-extrabold">How cashback works</h2>
            <p class="text-slate-300 mt-2 max-w-2xl">Three simple steps. No coupons, no hassle — money lands in your wallet.</p>
            <div class="mt-10 grid md:grid-cols-3 gap-6">
                @foreach ([
                    ['search', 'Search & compare', 'Find the best price across all providers in one place.'],
                    ['mouse-pointer-click', 'Book via TravelCash', 'Click through and book on the provider as usual.'],
                    ['wallet', 'Earn cashback', 'We share our commission back to your wallet — withdraw to UPI/bank.'],
                ] as $i => $step)
                    <div class="flex gap-4">
                        <div class="shrink-0 grid place-items-center w-11 h-11 rounded-xl bg-white/10 text-accent">
                            <i data-lucide="{{ $step[0] }}" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <p class="text-sm text-slate-400">Step {{ $i + 1 }}</p>
                            <h3 class="font-semibold">{{ $step[1] }}</h3>
                            <p class="text-sm text-slate-300 mt-1">{{ $step[2] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- ===== FEATURED FLIGHTS + PACKAGES ===== --}}
@includeWhen(!empty($featured['flights']), 'partials.offer-rail', ['title' => 'Trending flights', 'subtitle' => 'Great fares with cashback on top.', 'offers' => $featured['flights'] ?? [], 'cta' => ['flights', 'View all flights']])
@includeWhen(!empty($featured['packages']), 'partials.offer-rail', ['title' => 'Featured packages', 'subtitle' => 'Curated holidays, fully loaded.', 'offers' => $featured['packages'] ?? [], 'cta' => ['packages', 'View all packages']])

{{-- ===== TESTIMONIALS ===== --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 py-14">
    <h2 class="font-display text-2xl sm:text-3xl font-extrabold text-center">Loved by travellers</h2>
    <div class="mt-8 grid md:grid-cols-3 gap-5">
        @foreach ([
            ['Aarav S.', 'Got ₹3,200 back on my Goa trip. Booking was exactly the same as always.'],
            ['Meera K.', 'The flight compare is genuinely faster than the apps I used before.'],
            ['Dev P.', 'Withdrew cashback to UPI in two days. Legit and simple.'],
        ] as $t)
            <figure class="card p-6">
                <div class="flex gap-1 text-warning">@for($i=0;$i<5;$i++)<i data-lucide="star" class="w-4 h-4 fill-warning"></i>@endfor</div>
                <blockquote class="mt-3 text-ink">“{{ $t[1] }}”</blockquote>
                <figcaption class="mt-4 text-sm font-semibold text-muted">{{ $t[0] }}</figcaption>
            </figure>
        @endforeach
    </div>
</section>

{{-- ===== FAQ ===== --}}
<section class="max-w-3xl mx-auto px-4 sm:px-6 py-14" x-data="{ open: 0 }">
    <h2 class="font-display text-2xl sm:text-3xl font-extrabold text-center mb-8">Frequently asked questions</h2>
    <div class="space-y-3">
        @foreach ([
            ['Is the cashback real money?', 'Yes. We earn an affiliate commission and share a configurable percentage of it back into your wallet, withdrawable to UPI/bank/PayPal.'],
            ['When does cashback get confirmed?', 'It starts as pending, becomes confirmed once the provider validates your booking, and is withdrawable after a short hold period.'],
            ['Does booking cost more via TravelCash?', 'No. You pay the same provider price — the cashback is on top.'],
            ['Which providers are supported?', 'Booking.com, Agoda, Expedia, MakeMyTrip, Goibibo, Cleartrip, Uber, Ola, Tripadvisor and more — added continuously.'],
        ] as $i => $faq)
            <div class="card overflow-hidden">
                <button @click="open === {{ $i }} ? open = null : open = {{ $i }}" class="w-full flex items-center justify-between p-4 text-left font-semibold">
                    {{ $faq[0] }}
                    <i data-lucide="chevron-down" class="w-5 h-5 text-muted transition" :class="open === {{ $i }} && 'rotate-180'"></i>
                </button>
                <div x-show="open === {{ $i }}" x-collapse><p class="px-4 pb-4 text-muted text-sm">{{ $faq[1] }}</p></div>
            </div>
        @endforeach
    </div>
</section>

{{-- ===== CTA ===== --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 pb-8">
    <div class="card p-10 text-center bg-gradient-to-br from-primary to-accent text-white">
        <h2 class="font-display text-3xl font-extrabold">Start earning on your next trip</h2>
        <p class="mt-2 text-white/90">Join free. Search, book, and watch your wallet grow.</p>
        <a href="{{ route('register') }}" class="btn bg-white text-primary mt-6 font-bold">Create your free account <i data-lucide="arrow-right" class="w-4 h-4"></i></a>
    </div>
</section>

@push('scripts')
@endpush
@endsection
