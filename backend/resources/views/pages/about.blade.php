@extends('layouts.app')
@section('title', 'About TripCash — India\'s Smartest Travel Cashback Platform')

@section('content')
{{-- ===== HERO ===== --}}
<section class="relative hero-aurora">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 pt-16 pb-12 sm:pt-24 sm:pb-16">
        <div class="max-w-3xl mx-auto text-center">
            <span class="pill pill-brand fade-up">About TripCash</span>
            <h1 class="fade-up-2 mt-4 font-display text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight leading-tight">
                We make travel <span class="text-gradient">rewarding</span> for everyone
            </h1>
            <p class="fade-up-3 mt-5 text-lg sm:text-xl text-muted max-w-2xl mx-auto leading-relaxed">
                TripCash is India's first travel meta-search and cashback platform. We help you find the best
                deals across all major providers and earn real money back on every booking.
            </p>
        </div>
    </div>
</section>

{{-- ===== WHAT IS TRAVELCASH ===== --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 py-16 sm:py-20">
    <div class="grid lg:grid-cols-2 gap-12 items-center">
        <div>
            <span class="pill pill-brand">Our Platform</span>
            <h2 class="mt-3 font-display text-3xl sm:text-4xl font-extrabold leading-tight">
                What is TripCash?
            </h2>
            <p class="mt-5 text-muted text-lg leading-relaxed">
                TripCash is a <strong class="text-ink">travel meta-search engine with built-in cashback</strong>.
                Think of us as your one-stop travel companion that does two things brilliantly:
            </p>
            <div class="mt-6 space-y-4">
                <div class="flex gap-4 items-start">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-100 grid place-items-center shrink-0">
                        <i data-lucide="search" class="w-5 h-5 text-blue-600"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-ink">Compare prices across all providers</h3>
                        <p class="text-muted text-sm mt-1">We aggregate flights, hotels, trains, cabs, and packages from 9+ top providers like MakeMyTrip, Booking.com, Goibibo, Cleartrip, Agoda, Expedia, and more — showing you the best prices in one place.</p>
                    </div>
                </div>
                <div class="flex gap-4 items-start">
                    <div class="w-10 h-10 rounded-xl bg-green-50 border border-green-100 grid place-items-center shrink-0">
                        <i data-lucide="wallet" class="w-5 h-5 text-green-600"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-ink">Pay you real cashback</h3>
                        <p class="text-muted text-sm mt-1">When you book through us, we earn a commission from the provider. Instead of keeping it all, we share up to 40% of it back with you as real, withdrawable cashback — not fake points or coupons.</p>
                    </div>
                </div>
            </div>
            <p class="mt-6 text-muted leading-relaxed">
                You always pay the <strong class="text-ink">exact same price</strong> as you would on the provider's website directly.
                The cashback is additional savings that come from our commission — it's money you'd otherwise never see.
            </p>
        </div>
        <div>
            <div class="card p-8 relative overflow-hidden">
                <div class="absolute -right-12 -top-12 w-40 h-40 rounded-full blur-3xl bg-brand/20"></div>
                <div class="absolute -left-12 -bottom-12 w-40 h-40 rounded-full blur-3xl bg-primary/15"></div>
                <div class="relative text-center">
                    <div class="w-20 h-20 rounded-2xl mx-auto grid place-items-center text-white shadow-lift" style="background:linear-gradient(150deg,#14b8a6,#0d9488)">
                        <i data-lucide="plane" class="w-10 h-10"></i>
                    </div>
                    <h3 class="mt-4 font-display font-extrabold text-xl">Trip<span class="text-brand">Cash</span></h3>
                    <p class="text-muted text-sm mt-2">Travel more. Pay less. Earn cashback.</p>
                    <div class="mt-6 grid grid-cols-3 gap-4">
                        <div class="text-center">
                            <p class="text-2xl font-extrabold font-display text-ink">9+</p>
                            <p class="text-xs text-muted">Providers</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-extrabold font-display text-ink">50K+</p>
                            <p class="text-xs text-muted">Users</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-extrabold font-display text-ink">₹12L+</p>
                            <p class="text-xs text-muted">Cashback Paid</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ===== HOW WE MAKE MONEY - TRANSPARENCY ===== --}}
<section class="py-16 sm:py-20 bg-white border-y border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="text-center max-w-2xl mx-auto">
            <span class="pill pill-brand">Full Transparency</span>
            <h2 class="mt-3 font-display text-3xl sm:text-4xl font-extrabold">How we make money (and how you earn)</h2>
            <p class="text-muted mt-3 text-lg">No hidden tricks. Here's our business model in plain language.</p>
        </div>

        <div class="mt-12 max-w-4xl mx-auto">
            <div class="card p-8 sm:p-10 relative overflow-hidden" style="background:linear-gradient(135deg,#0B1220 0%,#0f2e2b 60%,#0d3a52 100%)">
                <div class="absolute -right-16 -top-16 w-60 h-60 rounded-full blur-3xl" style="background:rgba(13,148,136,.3)"></div>
                <div class="absolute -left-16 bottom-0 w-60 h-60 rounded-full blur-3xl" style="background:rgba(37,99,235,.2)"></div>
                <div class="relative text-white">
                    <div class="grid md:grid-cols-4 gap-6 items-center">
                        {{-- Step 1 --}}
                        <div class="text-center">
                            <div class="w-14 h-14 rounded-2xl mx-auto grid place-items-center" style="background:rgba(255,255,255,.1)">
                                <i data-lucide="user" class="w-6 h-6 text-teal-300"></i>
                            </div>
                            <p class="mt-3 font-semibold text-sm">You book a trip</p>
                            <p class="text-xs text-slate-400 mt-1">Through our platform link</p>
                        </div>
                        {{-- Arrow --}}
                        <div class="hidden md:flex justify-center">
                            <i data-lucide="arrow-right" class="w-6 h-6 text-slate-500"></i>
                        </div>
                        {{-- Step 2 --}}
                        <div class="text-center">
                            <div class="w-14 h-14 rounded-2xl mx-auto grid place-items-center" style="background:rgba(255,255,255,.1)">
                                <i data-lucide="building-2" class="w-6 h-6 text-blue-300"></i>
                            </div>
                            <p class="mt-3 font-semibold text-sm">Provider pays us</p>
                            <p class="text-xs text-slate-400 mt-1">Affiliate commission (5-15%)</p>
                        </div>
                        {{-- Arrow --}}
                        <div class="hidden md:flex justify-center">
                            <i data-lucide="arrow-right" class="w-6 h-6 text-slate-500"></i>
                        </div>
                    </div>
                    <div class="grid md:grid-cols-2 gap-6 mt-8 pt-8 border-t border-white/10">
                        <div class="text-center p-4 rounded-xl" style="background:rgba(34,197,94,.1);border:1px solid rgba(34,197,94,.2)">
                            <p class="text-green-400 font-bold text-lg">Up to 40%</p>
                            <p class="text-sm text-slate-300 mt-1">Goes to YOU as cashback</p>
                        </div>
                        <div class="text-center p-4 rounded-xl" style="background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1)">
                            <p class="text-slate-200 font-bold text-lg">Remaining</p>
                            <p class="text-sm text-slate-300 mt-1">Keeps TripCash running</p>
                        </div>
                    </div>
                    <p class="text-center text-xs text-slate-400 mt-6">You always pay the same price as booking directly. The cashback comes from the commission — not from your pocket.</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ===== WHAT MAKES US DIFFERENT ===== --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 py-16 sm:py-20">
    <div class="text-center max-w-2xl mx-auto">
        <span class="pill pill-brand">Our Difference</span>
        <h2 class="mt-3 font-display text-3xl sm:text-4xl font-extrabold">What makes TripCash unique?</h2>
        <p class="text-muted mt-3 text-lg">Built by travellers, for travellers. Here's what sets us apart.</p>
    </div>

    <div class="mt-12 grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ([
            ['indian-rupee', 'Real Money, Not Points', 'Unlike hotel loyalty points or airline miles that expire and have restrictions, we give you actual rupees. Withdraw to UPI, bank, or PayPal — no minimum lock-in period.', 'bg-green-50 text-green-600 border-green-100'],
            ['search', 'Meta-Search Power', 'Why check 10 apps when you can compare all of them in one search? We pull real-time prices from MakeMyTrip, Booking.com, Goibibo, Cleartrip, and more.', 'bg-blue-50 text-blue-600 border-blue-100'],
            ['brain', 'AI Travel Assistant', 'Our AI doesn\'t just search — it understands your preferences, budget, and travel style to recommend perfect destinations, itineraries, and deals.', 'bg-purple-50 text-purple-600 border-purple-100'],
            ['shield-check', 'Zero Risk Booking', 'You book directly with the provider. We never handle your payment or personal details. If anything goes wrong, the provider\'s full support applies.', 'bg-teal-50 text-teal-600 border-teal-100'],
            ['smartphone', 'Built for India', 'UPI withdrawals, OTP-based login, Hindi support, mobile-first PWA design — built for how Indians actually travel and transact.', 'bg-orange-50 text-orange-600 border-orange-100'],
            ['eye', 'Full Transparency', 'Track every cashback from pending → confirmed → withdrawn. See exactly how much commission was earned and how much was shared with you.', 'bg-pink-50 text-pink-600 border-pink-100'],
        ] as $feature)
            <div class="card card-hover p-6">
                <div class="w-12 h-12 rounded-xl {{ $feature[3] }} border grid place-items-center mb-4">
                    <i data-lucide="{{ $feature[0] }}" class="w-5 h-5"></i>
                </div>
                <h3 class="font-semibold text-lg">{{ $feature[1] }}</h3>
                <p class="text-muted mt-2 text-sm leading-relaxed">{{ $feature[2] }}</p>
            </div>
        @endforeach
    </div>
</section>

{{-- ===== HOW IT WORKS DETAILED ===== --}}
<section class="py-16 sm:py-20 bg-white border-y border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="text-center max-w-2xl mx-auto">
            <span class="pill pill-brand">Step by Step</span>
            <h2 class="mt-3 font-display text-3xl sm:text-4xl font-extrabold">How to use TripCash</h2>
            <p class="text-muted mt-3 text-lg">It's incredibly simple — here's the complete process from sign-up to withdrawal.</p>
        </div>

        <div class="mt-12 max-w-4xl mx-auto space-y-6">
            @foreach ([
                ['user-plus', 'Create Your Free Account', 'Sign up in 30 seconds with your email, phone number, or Google account. No credit card needed, no hidden charges. It\'s completely free — forever.', 'Step 1', 'bg-blue-50 text-blue-600 border-blue-100'],
                ['search', 'Search & Compare', 'Enter where you want to go, your dates, and preferences. We\'ll instantly show you prices from all major providers side-by-side. Hotels, flights, trains, cabs, packages — everything in one search.', 'Step 2', 'bg-teal-50 text-teal-600 border-teal-100'],
                ['mouse-pointer-click', 'Book Through Our Link', 'Found a great deal? Click the "Book Now" button. You\'ll be taken to the provider\'s website (MakeMyTrip, Booking.com, etc.) where you complete the booking normally. Same experience, same price.', 'Step 3', 'bg-purple-50 text-purple-600 border-purple-100'],
                ['clock', 'Cashback Gets Tracked', 'Once you complete the booking, our system automatically tracks it. You\'ll see "Pending" cashback appear in your wallet within a few hours. This tracks the booking confirmation process.', 'Step 4', 'bg-orange-50 text-orange-600 border-orange-100'],
                ['check-circle', 'Cashback Confirmed', 'After the provider confirms your booking wasn\'t cancelled (typically 30-90 days depending on the provider), your cashback moves from "Pending" to "Confirmed" — now it\'s yours to withdraw.', 'Step 5', 'bg-green-50 text-green-600 border-green-100'],
                ['banknote', 'Withdraw to UPI/Bank', 'Hit the withdrawal button, choose UPI (Google Pay, PhonePe, Paytm), bank transfer, or PayPal. After one-time KYC verification, money lands in your account within 24-48 hours.', 'Step 6', 'bg-pink-50 text-pink-600 border-pink-100'],
            ] as $step)
                <div class="card p-6 flex gap-5 items-start">
                    <div class="shrink-0">
                        <div class="w-12 h-12 rounded-xl {{ $step[4] }} border grid place-items-center">
                            <i data-lucide="{{ $step[0] }}" class="w-5 h-5"></i>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-muted uppercase tracking-wide">{{ $step[3] }}</span>
                        </div>
                        <h3 class="font-semibold text-lg mt-1">{{ $step[1] }}</h3>
                        <p class="text-muted text-sm leading-relaxed mt-1">{{ $step[2] }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-10 text-center">
            <a href="{{ route('register') }}" class="btn btn-primary text-base px-7 py-3.5">
                Start your journey now <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </a>
        </div>
    </div>
</section>

{{-- ===== PLATFORM FEATURES ===== --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 py-16 sm:py-20">
    <div class="text-center max-w-2xl mx-auto">
        <span class="pill pill-brand">Platform Features</span>
        <h2 class="mt-3 font-display text-3xl sm:text-4xl font-extrabold">Everything inside TripCash</h2>
        <p class="text-muted mt-3 text-lg">A comprehensive platform built for the modern Indian traveller.</p>
    </div>

    <div class="mt-12 grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
        @foreach ([
            ['search', 'Multi-Provider Search', 'Compare flights, hotels, trains, cabs & packages from 9+ providers instantly.'],
            ['wallet', 'Digital Wallet', 'Track all your cashback earnings — pending, confirmed, and withdrawn — in one place.'],
            ['brain', 'AI Recommendations', 'Get personalized travel suggestions based on your preferences, budget, and history.'],
            ['bell', 'Price Alerts', 'Set alerts for price drops on your favorite routes and destinations.'],
            ['heart', 'Saved Favorites', 'Bookmark deals you love and come back to them later when you\'re ready to book.'],
            ['users', 'Referral Program', 'Invite friends and earn bonus cashback when they make their first booking.'],
            ['headphones', 'Priority Support', 'Get help with any booking or cashback questions through our support system.'],
            ['shield', 'KYC & Security', 'Bank-grade security with KYC verification to protect your earnings and withdrawals.'],
            ['trending-up', 'Cashback Tracking', 'Real-time tracking from click to booking to confirmation to withdrawal.'],
            ['globe', 'Domestic & International', 'Works for both India travel and international trips — Bali, Dubai, Thailand, Europe.'],
            ['percent', 'Boosted Cashback', 'Special promotions with increased cashback rates on select providers and destinations.'],
            ['smartphone', 'PWA Mobile App', 'Install on your phone like a native app — fast, offline-capable, and notification-enabled.'],
        ] as $feature)
            <div class="card p-5 hover:border-brand/30 transition-colors">
                <i data-lucide="{{ $feature[0] }}" class="w-5 h-5 text-brand mb-3"></i>
                <h3 class="font-semibold text-sm">{{ $feature[1] }}</h3>
                <p class="text-xs text-muted mt-1 leading-relaxed">{{ $feature[2] }}</p>
            </div>
        @endforeach
    </div>
</section>

{{-- ===== SUPPORTED PROVIDERS ===== --}}
<section class="py-14 bg-white border-y border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="text-center max-w-2xl mx-auto">
            <span class="pill pill-brand">Our Partners</span>
            <h2 class="mt-3 font-display text-2xl sm:text-3xl font-extrabold">Providers we compare</h2>
            <p class="text-muted mt-2">All the brands you trust, in one place. More being added regularly.</p>
        </div>

        <div class="mt-10 grid grid-cols-3 sm:grid-cols-5 lg:grid-cols-9 gap-4">
            @foreach ([
                ['Booking.com', 'Hotels globally'],
                ['MakeMyTrip', 'Flights & hotels'],
                ['Goibibo', 'Flights & buses'],
                ['Cleartrip', 'Flights & trains'],
                ['Agoda', 'Hotels Asia'],
                ['Expedia', 'Full travel'],
                ['Tripadvisor', 'Reviews & tours'],
                ['Uber', 'City rides'],
                ['Ola', 'Cabs & autos'],
            ] as $provider)
                <div class="text-center p-3 rounded-xl bg-slate-50 hover:bg-slate-100 transition">
                    <div class="w-10 h-10 rounded-lg bg-white border border-slate-200 grid place-items-center mx-auto">
                        <i data-lucide="building-2" class="w-4 h-4 text-slate-500"></i>
                    </div>
                    <p class="font-semibold text-xs mt-2">{{ $provider[0] }}</p>
                    <p class="text-[10px] text-muted">{{ $provider[1] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ===== OUR MISSION & VISION ===== --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 py-16 sm:py-20">
    <div class="grid lg:grid-cols-2 gap-12">
        <div class="card p-8 relative overflow-hidden">
            <div class="absolute -right-8 -top-8 w-32 h-32 rounded-full blur-2xl bg-teal-100/60"></div>
            <div class="relative">
                <div class="w-12 h-12 rounded-xl bg-teal-50 border border-teal-100 grid place-items-center mb-4">
                    <i data-lucide="target" class="w-5 h-5 text-teal-600"></i>
                </div>
                <h3 class="font-display text-2xl font-extrabold">Our Mission</h3>
                <p class="text-muted mt-3 leading-relaxed">
                    To make every trip more rewarding for Indian travellers. We believe that when you book travel,
                    you deserve to get something back — not fake coupons or expiring points, but real money that
                    you can spend however you want.
                </p>
                <p class="text-muted mt-3 leading-relaxed">
                    We're here to bring transparency to travel bookings and ensure that the commissions earned from
                    your bookings benefit YOU, not just the middlemen.
                </p>
            </div>
        </div>

        <div class="card p-8 relative overflow-hidden">
            <div class="absolute -left-8 -bottom-8 w-32 h-32 rounded-full blur-2xl bg-blue-100/60"></div>
            <div class="relative">
                <div class="w-12 h-12 rounded-xl bg-blue-50 border border-blue-100 grid place-items-center mb-4">
                    <i data-lucide="eye" class="w-5 h-5 text-blue-600"></i>
                </div>
                <h3 class="font-display text-2xl font-extrabold">Our Vision</h3>
                <p class="text-muted mt-3 leading-relaxed">
                    To become India's most trusted travel platform where every booking = savings. We envision a future
                    where no one books travel without checking TripCash first — because why leave money on the table?
                </p>
                <p class="text-muted mt-3 leading-relaxed">
                    We aim to build an ecosystem where travel, savings, and smart planning come together seamlessly,
                    powered by AI and driven by a community of savvy travellers.
                </p>
            </div>
        </div>
    </div>
</section>

{{-- ===== WHO IS IT FOR ===== --}}
<section class="py-16 bg-white border-y border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="text-center max-w-2xl mx-auto">
            <span class="pill pill-brand">For Everyone</span>
            <h2 class="mt-3 font-display text-3xl sm:text-4xl font-extrabold">Who is TripCash for?</h2>
            <p class="text-muted mt-3 text-lg">Whether you travel once a year or once a week — TripCash saves you money.</p>
        </div>

        <div class="mt-12 grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach ([
                ['plane', 'Frequent Flyers', 'Business travellers who fly weekly. Small cashback per trip adds up to thousands every month.', '₹12,000+/month potential'],
                ['heart', 'Family Vacationers', 'Families planning annual or quarterly holidays. Save big on hotel + flight combos.', 'Average ₹3,500 per trip'],
                ['backpack', 'Budget Backpackers', 'Solo and budget travellers who want every rupee to count. Compare to find cheapest + earn cashback.', 'Every saving matters'],
                ['briefcase', 'Corporate Travellers', 'Companies booking for teams. Cashback on business travel = free team lunches.', 'Scale with every booking'],
            ] as $persona)
                <div class="card card-hover p-6 text-center">
                    <div class="w-14 h-14 rounded-2xl bg-brand/10 grid place-items-center mx-auto mb-4">
                        <i data-lucide="{{ $persona[0] }}" class="w-6 h-6 text-brand"></i>
                    </div>
                    <h3 class="font-semibold">{{ $persona[1] }}</h3>
                    <p class="text-muted text-sm mt-2 leading-relaxed">{{ $persona[2] }}</p>
                    <span class="pill pill-cashback mt-3 text-xs">{{ $persona[3] }}</span>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ===== STATS ===== --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 py-16"
         x-data="{ stats: [{n:1200000,l:'Total Cashback Paid',p:'₹',desc:'Real money returned to our users'},{n:50000,l:'Happy Travellers',s:'+',desc:'Growing community across India'},{n:9,l:'Provider Partners',s:'+',desc:'All major travel brands'},{n:4.8,l:'User Rating',d:1,desc:'Based on user reviews'},{n:150000,l:'Bookings Tracked',s:'+',desc:'Successful cashback bookings'},{n:48,l:'Avg Payout Time',s:'hrs',desc:'Fast withdrawal processing'}] }">
    <div class="text-center max-w-2xl mx-auto mb-10">
        <span class="pill pill-brand">Our Numbers</span>
        <h2 class="mt-3 font-display text-3xl sm:text-4xl font-extrabold">TripCash in numbers</h2>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <template x-for="s in stats">
            <div class="card p-5 text-center hover:border-brand/30 transition-colors">
                <p class="text-2xl font-extrabold font-display counter text-ink"
                   x-data="{v:0}" x-intersect.once="let t=s.n,step=t/40,i=setInterval(()=>{v+=step;if(v>=t){v=t;clearInterval(i)};$el.textContent=(s.p||'')+(s.d?v.toFixed(1):Math.floor(v).toLocaleString())+(s.s||'')},25)">0</p>
                <p class="text-sm font-semibold text-ink mt-1" x-text="s.l"></p>
                <p class="text-xs text-muted mt-0.5" x-text="s.desc"></p>
            </div>
        </template>
    </div>
</section>

{{-- ===== CTA ===== --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 pb-16">
    <div class="card p-10 sm:p-14 text-center relative overflow-hidden" style="background:linear-gradient(135deg,#0d9488 0%,#10b981 40%,#2563EB 130%)">
        <div class="absolute inset-0 ring-grid opacity-30"></div>
        <div class="relative">
            <h2 class="font-display text-3xl sm:text-4xl font-extrabold text-white">Ready to start earning?</h2>
            <p class="mt-4 text-white/90 text-lg max-w-xl mx-auto">
                Join 50,000+ smart Indian travellers who earn real cashback on every trip. Free to join, zero risk.
            </p>
            <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="{{ route('register') }}" class="btn btn-white font-bold text-base px-7 py-3.5">
                    Create your free account <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
                <a href="{{ route('contact') }}" class="btn font-semibold text-base px-6 py-3.5" style="background:rgba(255,255,255,.15);color:#fff;border:1px solid rgba(255,255,255,.3)">
                    <i data-lucide="mail" class="w-4 h-4"></i> Contact us
                </a>
            </div>
            <p class="mt-4 text-white/70 text-sm">No credit card needed. Start earning in under 60 seconds.</p>
        </div>
    </div>
</section>
@endsection
