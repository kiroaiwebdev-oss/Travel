@extends('layouts.app')

@section('content')
{{-- ===== HERO ===== --}}
<section class="relative hero-aurora">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 pt-14 pb-12 sm:pt-20 sm:pb-20">
        <div class="max-w-3xl mx-auto text-center">
            <a href="#cashback" class="fade-up inline-flex items-center gap-2 pill pill-cashback mx-auto hover:scale-[1.02] transition">
                <i data-lucide="sparkles" class="w-3.5 h-3.5"></i>
                Earn up to {{ (int) \App\Models\Setting::get('cashback.default_share_percent', 40) }}% of our commission back
                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
            </a>

            <h1 class="fade-up-2 mt-6 font-display text-[2.6rem] leading-[1.04] sm:text-6xl font-extrabold tracking-tight">
                Travel smarter.<br>Get <span class="text-gradient">real cashback</span><br class="sm:hidden"> on every booking.
            </h1>

            <p class="fade-up-3 mt-6 text-lg text-muted max-w-xl mx-auto">
                Compare flights, hotels, trains, cabs &amp; packages across every top provider —
                book as usual and we pay you back into your wallet.
            </p>

            {{-- social proof --}}
            <div class="fade-up-3 mt-7 flex items-center justify-center gap-3 text-sm text-muted">
                <div class="flex -space-x-2">
                    @foreach (['a','b','c','d'] as $s)
                        <img src="https://i.pravatar.cc/40?img={{ $loop->index + 11 }}" class="w-7 h-7 rounded-full ring-2 ring-white" alt="">
                    @endforeach
                </div>
                <span class="flex items-center gap-1">
                    <span class="flex text-warning">@for($i=0;$i<5;$i++)<i data-lucide="star" class="w-3.5 h-3.5 fill-warning"></i>@endfor</span>
                    <span class="font-semibold text-ink">4.8</span> · loved by 50k+ travellers
                </span>
            </div>
        </div>

        {{-- search widget --}}
        <div class="mt-10 max-w-5xl mx-auto fade-up-3">
            <x-search-widget :categories="$categories" active="hotels" />
        </div>

        {{-- trust counters --}}
        <div class="mt-10 grid grid-cols-2 sm:grid-cols-4 gap-3 max-w-4xl mx-auto"
             x-data="{ stats: [{n:1200000,l:'Paid as cashback',p:'₹'},{n:9,l:'Providers compared',s:'+'},{n:50000,l:'Happy travellers',s:'+'},{n:4.8,l:'Average rating',d:1}] }">
            <template x-for="s in stats">
                <div class="card p-4 text-center">
                    <p class="text-2xl font-extrabold font-display counter text-ink"
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
            <span class="pill pill-brand">Trending now</span>
            <h2 class="mt-2 font-display text-2xl sm:text-3xl font-extrabold">Popular destinations</h2>
        </div>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        @foreach ($destinations as $d)
            <a href="{{ route('search', ['category' => 'hotels', 'destination' => $d['name']]) }}"
               class="group relative aspect-[3/4] rounded-xl2 overflow-hidden card-hover">
                <img src="{{ $d['image'] }}" alt="{{ $d['name'] }}" loading="lazy" class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition duration-700">
                <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/15 to-transparent"></div>
                <div class="absolute bottom-3 left-3 text-white">
                    <p class="text-[11px] font-medium opacity-80">{{ $d['tag'] }}</p>
                    <p class="font-bold">{{ $d['name'] }}</p>
                </div>
            </a>
        @endforeach
    </div>
</section>

@includeWhen(!empty($featured['hotels']), 'partials.offer-rail', ['title' => 'Featured hotels', 'subtitle' => 'Hand-picked stays with boosted cashback.', 'offers' => $featured['hotels'] ?? [], 'cta' => ['hotels', 'View all hotels']])

{{-- ===== CASHBACK EXPLAINER ===== --}}
<section id="cashback" class="max-w-7xl mx-auto px-4 sm:px-6 py-16">
    <div class="card p-8 sm:p-12 text-white relative overflow-hidden" style="background:linear-gradient(135deg,#0B1220 0%,#0f2e2b 60%,#0d3a52 100%)">
        <div class="absolute -right-20 -top-20 w-80 h-80 rounded-full blur-3xl" style="background:rgba(13,148,136,.35)"></div>
        <div class="absolute -left-16 bottom-0 w-72 h-72 rounded-full blur-3xl" style="background:rgba(37,99,235,.25)"></div>
        <div class="relative">
            <span class="pill" style="background:rgba(255,255,255,.1);color:#fff">How it works</span>
            <h2 class="mt-3 font-display text-3xl font-extrabold">Cashback in three simple steps</h2>
            <p class="text-slate-300 mt-2 max-w-2xl">No coupons, no catch — real money lands in your wallet.</p>
            <div class="mt-10 grid md:grid-cols-3 gap-6">
                @foreach ([
                    ['search', 'Search & compare', 'Find the best price across every provider in one place.', '#14b8a6'],
                    ['mouse-pointer-click', 'Book via TravelCash', 'Click through and book on the provider exactly as usual.', '#2563EB'],
                    ['wallet', 'Earn cashback', 'We share our commission back — withdraw to UPI or bank.', '#10b981'],
                ] as $i => $step)
                    <div class="flex gap-4">
                        <div class="shrink-0 grid place-items-center w-12 h-12 rounded-2xl text-white font-bold" style="background:{{ $step[3] }}1f;color:{{ $step[3] }}">
                            <i data-lucide="{{ $step[0] }}" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400">Step {{ $i + 1 }}</p>
                            <h3 class="font-semibold">{{ $step[1] }}</h3>
                            <p class="text-sm text-slate-300 mt-1">{{ $step[2] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

@includeWhen(!empty($featured['flights']), 'partials.offer-rail', ['title' => 'Trending flights', 'subtitle' => 'Great fares with cashback on top.', 'offers' => $featured['flights'] ?? [], 'cta' => ['flights', 'View all flights']])
@includeWhen(!empty($featured['packages']), 'partials.offer-rail', ['title' => 'Featured packages', 'subtitle' => 'Curated holidays, fully loaded.', 'offers' => $featured['packages'] ?? [], 'cta' => ['packages', 'View all packages']])

{{-- ===== TESTIMONIALS ===== --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 py-14">
    <div class="text-center max-w-xl mx-auto">
        <span class="pill pill-brand">Testimonials</span>
        <h2 class="mt-2 font-display text-2xl sm:text-3xl font-extrabold">Loved by travellers</h2>
    </div>
    <div class="mt-8 grid md:grid-cols-3 gap-5">
        @foreach ([
            ['Aarav S.', 'Goa', 'Got ₹3,200 back on my Goa trip. Booking was exactly the same as always.'],
            ['Meera K.', 'Dubai', 'The flight compare is genuinely faster than the apps I used before.'],
            ['Dev P.', 'Manali', 'Withdrew cashback to UPI in two days. Legit and simple.'],
        ] as $t)
            <figure class="card card-hover p-6">
                <div class="flex gap-0.5 text-warning">@for($i=0;$i<5;$i++)<i data-lucide="star" class="w-4 h-4 fill-warning"></i>@endfor</div>
                <blockquote class="mt-3 text-ink leading-relaxed">“{{ $t[2] }}”</blockquote>
                <figcaption class="mt-4 flex items-center gap-3">
                    <span class="w-9 h-9 rounded-full grid place-items-center text-white text-sm font-bold" style="background:linear-gradient(150deg,#14b8a6,#0d9488)">{{ substr($t[0],0,1) }}</span>
                    <span><span class="font-semibold text-sm block">{{ $t[0] }}</span><span class="text-xs text-muted">Trip to {{ $t[1] }}</span></span>
                </figcaption>
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
                <button @click="open === {{ $i }} ? open = null : open = {{ $i }}" class="w-full flex items-center justify-between p-5 text-left font-semibold">
                    {{ $faq[0] }}
                    <i data-lucide="chevron-down" class="w-5 h-5 text-muted transition shrink-0" :class="open === {{ $i }} && 'rotate-180'"></i>
                </button>
                <div x-show="open === {{ $i }}" x-collapse><p class="px-5 pb-5 text-muted text-sm leading-relaxed">{{ $faq[1] }}</p></div>
            </div>
        @endforeach
    </div>
</section>

{{-- ===== CTA ===== --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 pb-10">
    <div class="card p-10 sm:p-14 text-center relative overflow-hidden ring-grid" style="background:linear-gradient(135deg,#0d9488 0%,#10b981 55%,#2563EB 130%)">
        <div class="relative">
            <h2 class="font-display text-3xl sm:text-4xl font-extrabold text-white">Start earning on your next trip</h2>
            <p class="mt-3 text-white/90">Join free in seconds. Search, book, and watch your wallet grow.</p>
            <a href="{{ route('register') }}" class="btn btn-white mt-7 font-bold">Create your free account <i data-lucide="arrow-right" class="w-4 h-4"></i></a>
        </div>
    </div>
</section>

@push('scripts')@endpush
@endsection
