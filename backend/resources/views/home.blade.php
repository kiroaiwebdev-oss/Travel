@extends('layouts.app')

@section('content')
{{-- ====================================================================
     MOBILE APP HOME (md:hidden) — native cashback-app feed experience
     ==================================================================== --}}
<div class="md:hidden">
    {{-- Search entry (tap → search) --}}
    <div class="px-4 pt-4">
        <a href="{{ route('search', ['category' => 'hotels']) }}" class="press flex items-center gap-3 card px-4 py-3.5">
            <i data-lucide="search" class="w-5 h-5 text-brand"></i>
            <span class="text-muted text-sm">Search hotels, flights, trains &amp; more…</span>
            <span class="ml-auto pill pill-cashback text-[10px]">Cashback</span>
        </a>
    </div>

    {{-- Quick actions (app shortcuts) --}}
    <div class="mt-5 grid grid-cols-5 gap-1 px-3">
        @foreach ([
            ['hotels', 'Hotels', 'bed', 'background:rgba(15,98,254,.1);color:#0F62FE'],
            ['flights', 'Flights', 'plane', 'background:rgba(0,184,169,.12);color:#009688'],
            ['trains', 'Trains', 'train-front', 'background:rgba(255,138,0,.12);color:#c2410c'],
            ['cabs', 'Cabs', 'car', 'background:rgba(168,85,247,.12);color:#9333ea'],
            ['packages', 'Packages', 'map', 'background:rgba(236,72,153,.12);color:#db2777'],
        ] as $qa)
            <a href="{{ route('search', ['category' => $qa[0]]) }}" class="qa press">
                <span class="qa-ic" style="{{ $qa[3] }}"><i data-lucide="{{ $qa[2] }}" class="w-5 h-5"></i></span>
                <span>{{ $qa[1] }}</span>
            </a>
        @endforeach
    </div>

    {{-- Earn / balance banner --}}
    <div class="px-4 mt-5">
        @auth
            <a href="{{ route('dashboard.wallet') }}" class="press block app-balance p-5">
                <div class="relative flex items-center justify-between">
                    <div>
                        <p class="text-xs text-white/70">Your cashback wallet</p>
                        <p class="text-3xl font-extrabold font-display mt-1">₹{{ number_format((float) (auth()->user()->wallet?->balance ?? 0), 0) }}</p>
                        <p class="text-xs text-white/70 mt-1">Tap to view &amp; withdraw</p>
                    </div>
                    <span class="grid place-items-center w-12 h-12 rounded-2xl" style="background:rgba(255,255,255,.12)">
                        <i data-lucide="wallet" class="w-6 h-6 text-white"></i>
                    </span>
                </div>
            </a>
        @else
            <a href="{{ route('register') }}" class="press block app-balance p-5">
                <div class="relative flex items-center justify-between gap-3">
                    <div>
                        <span class="pill text-[10px]" style="background:rgba(255,255,255,.15);color:#fff">Limited offer</span>
                        <p class="text-xl font-extrabold font-display mt-2 leading-tight">Earn up to {{ (int) \App\Models\Setting::get('cashback.default_share_percent', 40) }}% cashback on every trip</p>
                        <p class="text-xs text-white/70 mt-1">Join free → start earning real money</p>
                    </div>
                    <span class="grid place-items-center w-11 h-11 rounded-full shrink-0 bg-white text-ink">
                        <i data-lucide="arrow-right" class="w-5 h-5"></i>
                    </span>
                </div>
            </a>
        @endauth
    </div>

    {{-- Trending destinations — Instagram-style stories rail --}}
    <div class="mt-6">
        <div class="app-sec-title mb-3">
            <h3 class="font-display">Trending now</h3>
            <a href="{{ route('search', ['category' => 'hotels']) }}">See all</a>
        </div>
        <div class="h-scroll no-scrollbar px-4 pb-1">
            @foreach ($destinations as $d)
                <a href="{{ route('search', ['category' => 'hotels', 'destination' => $d['name']]) }}" class="story press text-center">
                    <span class="story-ring block"><span class="story-ring-inner block">
                        <img src="{{ $d['image'] }}" alt="{{ $d['name'] }}" loading="lazy">
                    </span></span>
                    <span class="block text-[11px] font-semibold mt-1.5 truncate">{{ $d['name'] }}</span>
                </a>
            @endforeach
        </div>
    </div>

    {{-- How it works — compact app cards --}}
    <div class="mt-6">
        <div class="app-sec-title mb-3">
            <h3 class="font-display">How it works</h3>
        </div>
        <div class="h-scroll no-scrollbar px-4 pb-1">
            @foreach ([
                ['search', 'Search & compare', 'Best prices across 9+ providers', 'background:rgba(0,184,169,.12);color:#009688'],
                ['mouse-pointer-click', 'Book as usual', 'Same provider, same price', 'background:rgba(15,98,254,.1);color:#0F62FE'],
                ['wallet', 'Earn cashback', 'Real money in your wallet', 'background:rgba(34,197,94,.12);color:#16a34a'],
            ] as $i => $s)
                <div class="card p-4 w-[62%] max-w-[15rem]">
                    <span class="grid place-items-center w-10 h-10 rounded-xl" style="{{ $s[3] }}"><i data-lucide="{{ $s[0] }}" class="w-5 h-5"></i></span>
                    <p class="text-[11px] text-muted mt-3 font-bold">STEP {{ $i + 1 }}</p>
                    <p class="font-semibold text-sm mt-0.5">{{ $s[1] }}</p>
                    <p class="text-xs text-muted mt-1">{{ $s[2] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</div>

{{-- ====================================================================
     DESKTOP HERO (hidden md:block) — full website landing
     ==================================================================== --}}
<section class="relative hero-aurora hidden md:block">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 pt-14 pb-12 sm:pt-24 sm:pb-24">
        <div class="max-w-4xl mx-auto text-center">
            <a href="#cashback" class="fade-up inline-flex items-center gap-2 pill pill-cashback mx-auto hover:scale-[1.02] transition">
                <i data-lucide="sparkles" class="w-3.5 h-3.5"></i>
                Earn up to {{ (int) \App\Models\Setting::get('cashback.default_share_percent', 40) }}% of our commission back as real cashback
                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
            </a>

            <h1 class="fade-up-2 mt-6 font-display text-[2.6rem] leading-[1.04] sm:text-6xl lg:text-7xl font-extrabold tracking-tight">
                India's smartest way to<br><span class="text-gradient">earn while you travel</span>
            </h1>

            <p class="fade-up-3 mt-6 text-lg sm:text-xl text-muted max-w-2xl mx-auto leading-relaxed">
                Compare flights, hotels, trains, cabs &amp; packages across 9+ top providers in one place.
                Book as you always do &mdash; we pay you real cashback into your wallet. No coupons, no tricks.
            </p>

            <div class="fade-up-3 mt-8 flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="{{ route('register') }}" class="btn btn-primary text-base px-7 py-3.5">
                    Start earning free <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
                <a href="#how-it-works" class="btn btn-ghost text-base px-6 py-3.5 border border-slate-200">
                    <i data-lucide="play-circle" class="w-5 h-5 text-brand"></i> See how it works
                </a>
            </div>

            <div class="fade-up-3 mt-8 flex items-center justify-center gap-3 text-sm text-muted">
                <div class="flex -space-x-2">
                    @foreach (['a','b','c','d','e'] as $s)
                        <img src="https://i.pravatar.cc/40?img={{ $loop->index + 11 }}" class="w-8 h-8 rounded-full ring-2 ring-white" alt="">
                    @endforeach
                </div>
                <span class="flex items-center gap-1.5">
                    <span class="flex text-warning">@for($i=0;$i<5;$i++)<i data-lucide="star" class="w-3.5 h-3.5 fill-warning"></i>@endfor</span>
                    <span class="font-semibold text-ink">4.8/5</span> &mdash; trusted by 50,000+ Indian travellers
                </span>
            </div>
        </div>

        <div class="mt-12 max-w-5xl mx-auto fade-up-3">
            <x-search-widget :categories="$categories" active="hotels" />
        </div>

        <div class="mt-12 grid grid-cols-2 sm:grid-cols-4 gap-4 max-w-4xl mx-auto"
             x-data="{ stats: [{n:1200000,l:'Cashback paid',p:'₹',desc:'Real money returned to users'},{n:9,l:'Providers compared',s:'+',desc:'All top Indian & global brands'},{n:50000,l:'Happy travellers',s:'+',desc:'And growing every day'},{n:4.8,l:'Average rating',d:1,desc:'On trust & review platforms'}] }">
            <template x-for="s in stats">
                <div class="card p-5 text-center group hover:border-brand/30 transition-colors">
                    <p class="text-2xl sm:text-3xl font-extrabold font-display counter text-ink"
                       x-data="{v:0}" x-intersect.once="let t=s.n,step=t/40,i=setInterval(()=>{v+=step;if(v>=t){v=t;clearInterval(i)};$el.textContent=(s.p||'')+(s.d?v.toFixed(1):Math.floor(v).toLocaleString())+(s.s||'')},25)">0</p>
                    <p class="text-sm font-semibold text-ink mt-1" x-text="s.l"></p>
                    <p class="text-xs text-muted mt-0.5" x-text="s.desc"></p>
                </div>
            </template>
        </div>
    </div>
</section>

{{-- ===== TRUSTED BY / PROVIDER LOGOS ===== --}}
<section class="py-8 sm:py-10 border-y border-slate-100 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <p class="text-center text-xs sm:text-sm font-semibold text-muted mb-5 sm:mb-6 uppercase tracking-wide">Compare prices across India's top travel providers</p>
        <div class="flex md:flex-wrap items-center md:justify-center gap-x-6 sm:gap-x-8 gap-y-4 overflow-x-auto no-scrollbar md:overflow-visible opacity-70 grayscale hover:grayscale-0 transition-all duration-500">
            @foreach (['Booking.com', 'MakeMyTrip', 'Goibibo', 'Cleartrip', 'Agoda', 'Expedia', 'Tripadvisor', 'Uber', 'Ola'] as $provider)
                <div class="flex items-center gap-2 text-sm font-bold text-slate-600 shrink-0">
                    <span class="w-8 h-8 rounded-lg bg-slate-100 grid place-items-center">
                        <i data-lucide="building-2" class="w-4 h-4"></i>
                    </span>
                    {{ $provider }}
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ===== WHAT IS TRAVELCASH - PLATFORM EXPLAINER ===== --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 py-12 sm:py-20">
    <div class="grid lg:grid-cols-2 gap-12 items-center">
        <div>
            <span class="pill pill-brand">What is TripCash?</span>
            <h2 class="mt-3 font-display text-3xl sm:text-4xl font-extrabold leading-tight">
                The travel platform that <span class="text-gradient">pays you back</span>
            </h2>
            <p class="mt-4 text-muted text-lg leading-relaxed">
                TripCash is India's first travel meta-search engine with built-in cashback. We aggregate deals from
                all major travel providers &mdash; flights, hotels, trains, cabs, and holiday packages &mdash; and show you
                the best prices in one place.
            </p>
            <p class="mt-3 text-muted leading-relaxed">
                When you book through us, we earn a small affiliate commission from the provider. Instead of keeping it all,
                we share a significant portion of that commission back with you as <strong class="text-ink">real cashback</strong>
                &mdash; withdrawable to your UPI, bank account, or PayPal.
            </p>
            <div class="mt-6 space-y-3">
                @foreach ([
                    ['check-circle', 'Same provider price — you never pay extra'],
                    ['check-circle', 'Real money in your wallet, not fake points or coupons'],
                    ['check-circle', 'Withdraw anytime to UPI, bank, or PayPal'],
                    ['check-circle', 'Compare 9+ providers in seconds'],
                    ['check-circle', 'AI-powered travel recommendations'],
                ] as $point)
                    <div class="flex items-start gap-3">
                        <i data-lucide="{{ $point[0] }}" class="w-5 h-5 text-brand shrink-0 mt-0.5"></i>
                        <span class="text-ink font-medium">{{ $point[1] }}</span>
                    </div>
                @endforeach
            </div>
            <a href="{{ route('about') }}" class="btn btn-brand mt-8">
                Learn more about us <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </a>
        </div>
        <div class="relative">
            <div class="card p-6 sm:p-8 relative overflow-hidden">
                <div class="absolute -right-10 -top-10 w-40 h-40 rounded-full blur-3xl bg-brand/20"></div>
                <div class="absolute -left-10 -bottom-10 w-40 h-40 rounded-full blur-3xl bg-primary/15"></div>
                <div class="relative space-y-4">
                    <div class="flex items-center gap-3 p-3 rounded-xl bg-green-50 border border-green-100">
                        <div class="w-10 h-10 rounded-full bg-green-100 grid place-items-center">
                            <i data-lucide="indian-rupee" class="w-5 h-5 text-green-600"></i>
                        </div>
                        <div>
                            <p class="text-xs text-muted">Cashback earned today</p>
                            <p class="font-bold text-green-700 text-lg">+₹2,340</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 p-3 rounded-xl bg-blue-50 border border-blue-100">
                        <div class="w-10 h-10 rounded-full bg-blue-100 grid place-items-center">
                            <i data-lucide="wallet" class="w-5 h-5 text-blue-600"></i>
                        </div>
                        <div>
                            <p class="text-xs text-muted">Total wallet balance</p>
                            <p class="font-bold text-blue-700 text-lg">₹12,450</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 p-3 rounded-xl bg-purple-50 border border-purple-100">
                        <div class="w-10 h-10 rounded-full bg-purple-100 grid place-items-center">
                            <i data-lucide="arrow-down-to-line" class="w-5 h-5 text-purple-600"></i>
                        </div>
                        <div>
                            <p class="text-xs text-muted">Withdrawn to UPI</p>
                            <p class="font-bold text-purple-700 text-lg">₹8,900</p>
                        </div>
                    </div>
                    <div class="text-center pt-3">
                        <span class="pill pill-cashback">Real money. Real withdrawals.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ===== POPULAR DESTINATIONS (desktop — mobile uses stories rail above) ===== --}}
<section class="hidden md:block max-w-7xl mx-auto px-4 sm:px-6 py-14">
    <div class="flex items-end justify-between mb-6">
        <div>
            <span class="pill pill-brand">Trending now</span>
            <h2 class="mt-2 font-display text-2xl sm:text-3xl font-extrabold">Popular destinations</h2>
            <p class="text-muted mt-1">Top picks loved by our community</p>
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

{{-- ===== HOW IT WORKS - DETAILED ===== --}}
<section id="how-it-works" class="max-w-7xl mx-auto px-4 sm:px-6 py-12 sm:py-20">
    <div class="text-center max-w-2xl mx-auto">
        <span class="pill pill-brand">Simple & transparent</span>
        <h2 class="mt-3 font-display text-3xl sm:text-4xl font-extrabold">How TripCash works</h2>
        <p class="text-muted mt-3 text-lg">Three simple steps to start earning real cashback on every trip. No hidden tricks, no complicated processes.</p>
    </div>

    <div class="mt-12 grid md:grid-cols-3 gap-8">
        @foreach ([
            ['search', 'Search & Compare', 'Enter your destination, dates, and preferences. We instantly compare prices across 9+ providers like MakeMyTrip, Booking.com, Goibibo, Cleartrip, and more to find you the best deals.', 'bg-teal-50 text-teal-600 border-teal-100', '01'],
            ['mouse-pointer-click', 'Book via TripCash', 'Found a great deal? Click through to the provider and complete your booking exactly as you normally would. Same price, same provider, same experience — we just track the referral.', 'bg-blue-50 text-blue-600 border-blue-100', '02'],
            ['wallet', 'Earn Real Cashback', 'Once your booking is confirmed by the provider, cashback lands in your TripCash wallet. Withdraw it to UPI, bank account, or PayPal — it\'s your real money.', 'bg-green-50 text-green-600 border-green-100', '03'],
        ] as $step)
            <div class="card card-hover p-6 sm:p-8 relative overflow-hidden group">
                <div class="absolute top-4 right-4 text-5xl font-extrabold font-display text-slate-100 group-hover:text-brand/10 transition">{{ $step[4] }}</div>
                <div class="relative">
                    <div class="w-14 h-14 rounded-2xl {{ $step[3] }} border grid place-items-center mb-5">
                        <i data-lucide="{{ $step[0] }}" class="w-6 h-6"></i>
                    </div>
                    <h3 class="font-display font-bold text-lg">{{ $step[1] }}</h3>
                    <p class="text-muted mt-2 leading-relaxed text-sm">{{ $step[2] }}</p>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-10 text-center">
        <a href="{{ route('register') }}" class="btn btn-primary">
            Get started — it's free <i data-lucide="arrow-right" class="w-4 h-4"></i>
        </a>
    </div>
</section>

{{-- ===== WHY CHOOSE US ===== --}}
<section class="py-12 sm:py-20 bg-white border-y border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="text-center max-w-2xl mx-auto">
            <span class="pill pill-brand">Why TripCash?</span>
            <h2 class="mt-3 font-display text-3xl sm:text-4xl font-extrabold">Everything you need for smarter travel</h2>
            <p class="text-muted mt-3 text-lg">We built the platform we wished existed as travellers ourselves.</p>
        </div>

        <div class="mt-12 grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ([
                ['piggy-bank', 'Real Cashback, Not Points', 'Unlike loyalty points that expire or have restrictions, we give you actual money back. Withdraw to UPI, bank, or PayPal anytime.', 'bg-green-50 text-green-600'],
                ['shield-check', 'Safe & Transparent', 'Every transaction is tracked, every cashback is auditable. You book directly with trusted providers — we never handle your payment.', 'bg-blue-50 text-blue-600'],
                ['zap', 'Lightning Fast Compare', 'Compare prices across 9+ providers in seconds. Our AI engine finds the best deals so you don\'t have to check 10 different apps.', 'bg-yellow-50 text-yellow-600'],
                ['brain', 'AI-Powered Recommendations', 'Our AI assistant learns your preferences and suggests personalized destinations, deals, and itineraries tailored just for you.', 'bg-purple-50 text-purple-600'],
                ['smartphone', 'Mobile-First Experience', 'Designed for the way Indians travel — quick, mobile-first, with UPI withdrawals, OTP login, and PWA support.', 'bg-orange-50 text-orange-600'],
                ['users', 'Referral Rewards', 'Invite friends and earn bonus cashback when they book. The more you share, the more you earn together.', 'bg-pink-50 text-pink-600'],
            ] as $feature)
                <div class="card card-hover p-6 group">
                    <div class="w-12 h-12 rounded-xl {{ $feature[3] }} grid place-items-center mb-4 group-hover:scale-110 transition">
                        <i data-lucide="{{ $feature[0] }}" class="w-5 h-5"></i>
                    </div>
                    <h3 class="font-semibold text-lg">{{ $feature[1] }}</h3>
                    <p class="text-muted mt-2 text-sm leading-relaxed">{{ $feature[2] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ===== CASHBACK EXPLAINER (DARK) ===== --}}
<section id="cashback" class="max-w-7xl mx-auto px-4 sm:px-6 py-16">
    <div class="card p-8 sm:p-12 text-white relative overflow-hidden" style="background:linear-gradient(135deg,#0B1220 0%,#0f2e2b 60%,#0d3a52 100%)">
        <div class="absolute -right-20 -top-20 w-80 h-80 rounded-full blur-3xl" style="background:rgba(13,148,136,.35)"></div>
        <div class="absolute -left-16 bottom-0 w-72 h-72 rounded-full blur-3xl" style="background:rgba(37,99,235,.25)"></div>
        <div class="relative">
            <span class="pill" style="background:rgba(255,255,255,.1);color:#fff">Cashback breakdown</span>
            <h2 class="mt-3 font-display text-3xl sm:text-4xl font-extrabold">Where does the cashback come from?</h2>
            <p class="text-slate-300 mt-2 max-w-3xl text-lg">It's simple economics, not magic. Here's exactly how it works:</p>

            <div class="mt-10 grid md:grid-cols-2 gap-8">
                <div class="space-y-6">
                    @foreach ([
                        ['building-2', 'Provider pays us commission', 'When you book through our link, the provider (MakeMyTrip, Booking.com, etc.) pays us an affiliate commission for sending you to them.'],
                        ['split', 'We split it with you', 'Instead of keeping the full commission, we share up to 40% of it with you. That\'s your cashback — real money, no strings.'],
                        ['wallet', 'Money lands in your wallet', 'Once the provider confirms your booking wasn\'t cancelled, cashback moves from "pending" to "withdrawable" in your wallet.'],
                        ['banknote', 'Withdraw anywhere', 'Hit the withdrawal button and get your money sent to UPI (Google Pay, PhonePe, Paytm), bank transfer, or PayPal.'],
                    ] as $i => $step)
                        <div class="flex gap-4">
                            <div class="shrink-0 w-10 h-10 rounded-xl grid place-items-center" style="background:rgba(255,255,255,.1)">
                                <i data-lucide="{{ $step[0] }}" class="w-5 h-5 text-brand-400"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold">{{ $step[1] }}</h3>
                                <p class="text-sm text-slate-300 mt-1">{{ $step[2] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="flex items-center justify-center">
                    <div class="w-full max-w-sm p-6 rounded-2xl" style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1)">
                        <p class="text-sm text-slate-400 mb-3">Example: You book a ₹15,000 hotel</p>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center py-2 border-b border-white/10">
                                <span class="text-slate-300 text-sm">Hotel price you pay</span>
                                <span class="font-bold">₹15,000</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-white/10">
                                <span class="text-slate-300 text-sm">Provider pays us (8%)</span>
                                <span class="font-bold text-slate-300">₹1,200</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-white/10">
                                <span class="text-slate-300 text-sm">We share with you (40%)</span>
                                <span class="font-bold text-green-400">₹480</span>
                            </div>
                            <div class="flex justify-between items-center py-2">
                                <span class="text-white font-semibold">Your cashback</span>
                                <span class="text-xl font-extrabold text-green-400">₹480</span>
                            </div>
                        </div>
                        <p class="text-xs text-slate-400 mt-3">* Commission rates vary by provider & category</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@includeWhen(!empty($featured['flights']), 'partials.offer-rail', ['title' => 'Trending flights', 'subtitle' => 'Great fares with cashback on top.', 'offers' => $featured['flights'] ?? [], 'cta' => ['flights', 'View all flights']])

{{-- ===== AI ASSISTANT SECTION ===== --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 py-12 sm:py-20">
    <div class="grid lg:grid-cols-2 gap-12 items-center">
        <div class="order-2 lg:order-1">
            <div class="card p-5 sm:p-6 relative overflow-hidden">
                <div class="absolute -right-8 -bottom-8 w-32 h-32 rounded-full blur-2xl bg-purple-200/50"></div>
                <div class="relative space-y-3">
                    <div class="flex gap-3 items-start">
                        <div class="w-8 h-8 rounded-full bg-purple-100 grid place-items-center shrink-0">
                            <i data-lucide="bot" class="w-4 h-4 text-purple-600"></i>
                        </div>
                        <div class="card p-3 text-sm bg-purple-50 border-purple-100">
                            I found 3 beachfront hotels in Goa under ₹5,000/night with 12-18% cashback. The best value is Hotel Paradise with ₹890 cashback on a 2-night stay. Want me to compare amenities?
                        </div>
                    </div>
                    <div class="flex gap-3 items-start justify-end">
                        <div class="card p-3 text-sm bg-blue-50 border-blue-100">
                            Yes! Also suggest what I can do in North Goa for 3 days.
                        </div>
                        <div class="w-8 h-8 rounded-full bg-blue-100 grid place-items-center shrink-0">
                            <i data-lucide="user" class="w-4 h-4 text-blue-600"></i>
                        </div>
                    </div>
                    <div class="flex gap-3 items-start">
                        <div class="w-8 h-8 rounded-full bg-purple-100 grid place-items-center shrink-0">
                            <i data-lucide="bot" class="w-4 h-4 text-purple-600"></i>
                        </div>
                        <div class="card p-3 text-sm bg-purple-50 border-purple-100">
                            Here's a 3-day North Goa itinerary: Day 1 — Calangute Beach & Fort Aguada, Day 2 — Anjuna Flea Market & Chapora Fort, Day 3 — Panjim Heritage Walk & Fontainhas. I'll also find cabs with cashback!
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="order-1 lg:order-2">
            <span class="pill pill-brand">AI-Powered</span>
            <h2 class="mt-3 font-display text-3xl sm:text-4xl font-extrabold leading-tight">
                Your personal AI travel assistant
            </h2>
            <p class="mt-4 text-muted text-lg leading-relaxed">
                Our AI assistant understands your budget, preferences, and travel style. Ask it anything &mdash;
                from finding the cheapest flights to planning entire itineraries.
            </p>
            <div class="mt-6 space-y-3">
                @foreach ([
                    'Find the best deals matching your budget instantly',
                    'Get personalized destination recommendations',
                    'Plan day-by-day itineraries with local tips',
                    'Compare hotels by amenities, reviews & cashback',
                    'Track price drops and notify you automatically',
                ] as $point)
                    <div class="flex items-start gap-3">
                        <i data-lucide="sparkles" class="w-4 h-4 text-purple-500 shrink-0 mt-1"></i>
                        <span class="text-ink text-sm font-medium">{{ $point }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

@includeWhen(!empty($featured['packages']), 'partials.offer-rail', ['title' => 'Featured packages', 'subtitle' => 'Curated holidays, fully loaded.', 'offers' => $featured['packages'] ?? [], 'cta' => ['packages', 'View all packages']])

{{-- ===== CATEGORIES SECTION ===== --}}
<section class="py-16 bg-white border-y border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="text-center max-w-2xl mx-auto">
            <span class="pill pill-brand">Everything you need</span>
            <h2 class="mt-3 font-display text-3xl sm:text-4xl font-extrabold">One platform, all travel needs</h2>
            <p class="text-muted mt-3">Stop switching between 5 different apps. We bring them all together.</p>
        </div>

        <div class="mt-12 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
            @foreach ([
                ['bed', 'Hotels & Stays', 'From budget to luxury, compare all', 'bg-blue-50 text-blue-600'],
                ['plane', 'Flights', 'Domestic & international fares', 'bg-teal-50 text-teal-600'],
                ['train-front', 'Trains', 'IRCTC and partner bookings', 'bg-orange-50 text-orange-600'],
                ['car', 'Cabs & Rides', 'Airport transfers & city rides', 'bg-purple-50 text-purple-600'],
                ['map', 'Packages', 'All-inclusive holiday packages', 'bg-pink-50 text-pink-600'],
            ] as $cat)
                <a href="{{ route('search', ['category' => strtolower(explode(' ', $cat[1])[0])]) }}" class="card card-hover p-5 text-center group">
                    <div class="w-12 h-12 rounded-xl {{ $cat[3] }} grid place-items-center mx-auto mb-3 group-hover:scale-110 transition">
                        <i data-lucide="{{ $cat[0] }}" class="w-5 h-5"></i>
                    </div>
                    <h3 class="font-semibold text-sm">{{ $cat[1] }}</h3>
                    <p class="text-xs text-muted mt-1">{{ $cat[2] }}</p>
                </a>
            @endforeach
        </div>
    </div>
</section>

{{-- ===== TESTIMONIALS ===== --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 py-12 sm:py-20">
    <div class="text-center max-w-xl mx-auto">
        <span class="pill pill-brand">Real stories</span>
        <h2 class="mt-2 font-display text-2xl sm:text-3xl font-extrabold">What our travellers say</h2>
        <p class="text-muted mt-2">Don't just take our word for it — hear from people who saved real money.</p>
    </div>
    <div class="mt-10 grid md:grid-cols-3 gap-6 max-md:flex max-md:overflow-x-auto max-md:no-scrollbar max-md:-mx-4 max-md:px-4 max-md:snap-x">
        @foreach ([
            ['Aarav S.', 'Goa Trip', '₹3,200 cashback', 'Got ₹3,200 back on my Goa trip. The booking experience was exactly the same as booking directly on MakeMyTrip. Money hit my UPI in 2 days after confirmation.', '5'],
            ['Meera K.', 'Dubai Holiday', '₹8,500 cashback', 'Used TripCash for my Dubai family trip. Compared flight + hotel prices in one place and earned ₹8,500 cashback. The AI even suggested the best areas to stay!', '5'],
            ['Dev P.', 'Manali Weekend', '₹1,800 cashback', 'Was skeptical at first but tried it for a Manali trip. Found cheaper hotels than what I saw on apps, plus got ₹1,800 back. Now I always check here first.', '5'],
            ['Priya R.', 'Kerala Backwaters', '₹4,100 cashback', 'Booked a houseboat and flights through TripCash. The whole family loved Kerala and I loved the ₹4,100 that came back into my wallet! So simple.', '5'],
            ['Rahul M.', 'Business Travel', '₹12,000 cashback', 'I travel for work almost weekly. Started using TripCash and I\'ve earned over ₹12,000 in just 3 months. It adds up fast when you travel often.', '5'],
            ['Ananya T.', 'Rajasthan Road Trip', '₹2,600 cashback', 'Booked cabs and hotels for our Rajasthan road trip. The compare feature saved us time and the cashback saved us money. Win-win!', '5'],
        ] as $t)
            <figure class="card card-hover p-6 max-md:w-[82%] max-md:shrink-0 max-md:snap-start">
                <div class="flex items-center justify-between">
                    <div class="flex gap-0.5 text-warning">@for($i=0;$i<(int)$t[4];$i++)<i data-lucide="star" class="w-4 h-4 fill-warning"></i>@endfor</div>
                    <span class="pill pill-cashback text-xs">{{ $t[2] }}</span>
                </div>
                <blockquote class="mt-4 text-ink leading-relaxed text-sm">"{{ $t[3] }}"</blockquote>
                <figcaption class="mt-5 flex items-center gap-3 pt-4 border-t border-slate-100">
                    <span class="w-10 h-10 rounded-full grid place-items-center text-white text-sm font-bold" style="background:linear-gradient(150deg,#14b8a6,#0d9488)">{{ substr($t[0],0,1) }}</span>
                    <span>
                        <span class="font-semibold text-sm block">{{ $t[0] }}</span>
                        <span class="text-xs text-muted">{{ $t[1] }}</span>
                    </span>
                </figcaption>
            </figure>
        @endforeach
    </div>
</section>

{{-- ===== SECURITY & TRUST ===== --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 py-12">
    <div class="card p-8 sm:p-10 bg-slate-50 border-slate-200">
        <div class="text-center max-w-2xl mx-auto">
            <h2 class="font-display text-2xl sm:text-3xl font-extrabold">Your security is our priority</h2>
            <p class="text-muted mt-2">We take data protection and financial security seriously.</p>
        </div>
        <div class="mt-8 grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach ([
                ['shield-check', 'Secure Bookings', 'Book directly with verified providers. We never store your payment details.'],
                ['lock', 'Data Encrypted', 'All sensitive data is encrypted at rest and in transit. SOC2-aligned practices.'],
                ['eye-off', 'Privacy First', 'We never sell your personal data. Minimal data collection, maximum transparency.'],
                ['badge-check', 'KYC Verified Payouts', 'Withdrawals require identity verification to prevent fraud and protect your earnings.'],
            ] as $trust)
                <div class="text-center">
                    <div class="w-12 h-12 rounded-xl bg-white border border-slate-200 grid place-items-center mx-auto mb-3">
                        <i data-lucide="{{ $trust[0] }}" class="w-5 h-5 text-brand"></i>
                    </div>
                    <h3 class="font-semibold text-sm">{{ $trust[1] }}</h3>
                    <p class="text-xs text-muted mt-1">{{ $trust[2] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ===== FAQ ===== --}}
<section class="max-w-3xl mx-auto px-4 sm:px-6 py-14" x-data="{ open: 0 }">
    <div class="text-center mb-10">
        <span class="pill pill-brand">Got questions?</span>
        <h2 class="mt-3 font-display text-2xl sm:text-3xl font-extrabold">Frequently asked questions</h2>
        <p class="text-muted mt-2">Everything you need to know about TripCash</p>
    </div>
    <div class="space-y-3">
        @foreach ([
            ['Is the cashback real money?', 'Yes, absolutely! We earn an affiliate commission when you book through us, and we share a significant percentage of it back to your TripCash wallet. This is real money — you can withdraw it to UPI (Google Pay, PhonePe, Paytm), bank transfer, or PayPal.'],
            ['Do I pay more when booking through TripCash?', 'No. You pay the exact same price as you would on the provider\'s website directly. The cashback comes from the commission the provider pays us — it doesn\'t affect your booking price at all.'],
            ['When does cashback get confirmed?', 'Cashback starts as "pending" when your booking is detected. Once the provider confirms your booking wasn\'t cancelled (typically 30-90 days), it becomes "confirmed" and is available for withdrawal.'],
            ['Which providers are supported?', 'We support Booking.com, Agoda, Expedia, MakeMyTrip, Goibibo, Cleartrip, Uber, Ola, Tripadvisor, and many more. We continuously add new providers to give you more options and better deals.'],
            ['How do I withdraw my cashback?', 'Go to your Wallet > Withdrawals, enter the amount, and choose UPI, bank transfer, or PayPal. After KYC verification, payouts are processed within 24-48 hours.'],
            ['Is there a minimum withdrawal amount?', 'Yes, there is a small minimum withdrawal amount to cover processing fees. You can see the current minimum in your wallet dashboard.'],
            ['How does the referral program work?', 'Share your unique referral link with friends. When they sign up and make their first booking, both of you earn bonus cashback. The more friends you invite, the more you earn!'],
            ['What is the AI assistant?', 'Our AI travel assistant helps you find the best deals, compare options, plan itineraries, and get personalized recommendations based on your budget and preferences. It\'s like having a travel agent in your pocket.'],
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

{{-- ===== FINAL CTA ===== --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 pb-16">
    <div class="card p-10 sm:p-14 text-center relative overflow-hidden" style="background:linear-gradient(135deg,#0d9488 0%,#10b981 40%,#2563EB 130%)">
        <div class="absolute inset-0 ring-grid opacity-30"></div>
        <div class="relative">
            <span class="pill" style="background:rgba(255,255,255,.2);color:#fff">Join 50,000+ smart travellers</span>
            <h2 class="mt-4 font-display text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white">Start earning on your next trip</h2>
            <p class="mt-4 text-white/90 text-lg max-w-xl mx-auto">
                Sign up free in 30 seconds. Search, compare, book &mdash; and watch real cashback land in your wallet.
            </p>
            <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="{{ route('register') }}" class="btn btn-white font-bold text-base px-7 py-3.5">
                    Create your free account <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
                <a href="{{ route('about') }}" class="btn font-semibold text-base px-6 py-3.5" style="background:rgba(255,255,255,.15);color:#fff;border:1px solid rgba(255,255,255,.3)">
                    Learn more about us
                </a>
            </div>
            <p class="mt-4 text-white/70 text-sm">No credit card required. Free forever.</p>
        </div>
    </div>
</section>

@push('scripts')@endpush
@endsection
