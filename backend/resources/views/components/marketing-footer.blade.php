<footer class="mt-24 bg-secondary text-slate-300">
    {{-- Newsletter Section --}}
    <div class="border-b border-white/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-12">
            <div class="grid md:grid-cols-2 gap-8 items-center">
                <div>
                    <h3 class="font-display text-xl sm:text-2xl font-extrabold text-white">Get exclusive deals in your inbox</h3>
                    <p class="text-slate-400 mt-2 text-sm">Join 50,000+ travellers who get the best cashback deals, travel tips, and exclusive offers delivered weekly.</p>
                </div>
                <form class="flex gap-2" action="#" method="POST">
                    @csrf
                    <input type="email" placeholder="Enter your email" class="flex-1 rounded-xl bg-white/10 border border-white/10 px-4 py-3 text-white placeholder-slate-400 text-sm focus:outline-none focus:border-brand/50 focus:ring-1 focus:ring-brand/30">
                    <button type="submit" class="btn btn-brand px-5 py-3 shrink-0">
                        Subscribe <i data-lucide="send" class="w-4 h-4"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Main Footer Content --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-14 grid gap-10 md:grid-cols-5">
        {{-- Brand Column --}}
        <div class="md:col-span-2">
            <div class="flex items-center">
                <x-brand-logo light />
            </div>
            <p class="mt-4 text-sm text-slate-400 max-w-xs leading-relaxed">
                {{ \App\Models\Setting::get('site.tagline', 'India\'s first travel meta-search engine with built-in cashback. Compare prices, book as usual, earn real money back.') }}
            </p>
            <div class="mt-5 flex items-center gap-3">
                <a href="#" class="w-9 h-9 rounded-lg bg-white/10 hover:bg-white/20 grid place-items-center transition" aria-label="Twitter">
                    <i data-lucide="twitter" class="w-4 h-4 text-white"></i>
                </a>
                <a href="#" class="w-9 h-9 rounded-lg bg-white/10 hover:bg-white/20 grid place-items-center transition" aria-label="Instagram">
                    <i data-lucide="instagram" class="w-4 h-4 text-white"></i>
                </a>
                <a href="#" class="w-9 h-9 rounded-lg bg-white/10 hover:bg-white/20 grid place-items-center transition" aria-label="LinkedIn">
                    <i data-lucide="linkedin" class="w-4 h-4 text-white"></i>
                </a>
                <a href="#" class="w-9 h-9 rounded-lg bg-white/10 hover:bg-white/20 grid place-items-center transition" aria-label="YouTube">
                    <i data-lucide="youtube" class="w-4 h-4 text-white"></i>
                </a>
            </div>
            {{-- Trust badges --}}
            <div class="mt-5 flex items-center gap-3">
                <span class="pill text-xs" style="background:rgba(34,197,94,.15);color:#4ade80;border:1px solid rgba(34,197,94,.2)">
                    <i data-lucide="shield-check" class="w-3 h-3"></i> Verified Platform
                </span>
                <span class="pill text-xs" style="background:rgba(255,255,255,.08);color:#94a3b8;border:1px solid rgba(255,255,255,.1)">
                    <i data-lucide="lock" class="w-3 h-3"></i> SSL Secured
                </span>
            </div>
        </div>

        {{-- Explore --}}
        <div>
            <h4 class="text-white font-semibold text-sm">Explore</h4>
            <ul class="mt-4 space-y-2.5 text-sm">
                <li><a class="hover:text-white transition flex items-center gap-2" href="{{ route('search', ['category' => 'hotels']) }}"><i data-lucide="bed" class="w-3.5 h-3.5 opacity-50"></i> Hotels</a></li>
                <li><a class="hover:text-white transition flex items-center gap-2" href="{{ route('search', ['category' => 'flights']) }}"><i data-lucide="plane" class="w-3.5 h-3.5 opacity-50"></i> Flights</a></li>
                <li><a class="hover:text-white transition flex items-center gap-2" href="{{ route('search', ['category' => 'trains']) }}"><i data-lucide="train-front" class="w-3.5 h-3.5 opacity-50"></i> Trains</a></li>
                <li><a class="hover:text-white transition flex items-center gap-2" href="{{ route('search', ['category' => 'cabs']) }}"><i data-lucide="car" class="w-3.5 h-3.5 opacity-50"></i> Cabs</a></li>
                <li><a class="hover:text-white transition flex items-center gap-2" href="{{ route('search', ['category' => 'packages']) }}"><i data-lucide="map" class="w-3.5 h-3.5 opacity-50"></i> Packages</a></li>
            </ul>
        </div>

        {{-- Company --}}
        <div>
            <h4 class="text-white font-semibold text-sm">Company</h4>
            <ul class="mt-4 space-y-2.5 text-sm">
                <li><a class="hover:text-white transition" href="{{ route('about') }}">About us</a></li>
                <li><a class="hover:text-white transition" href="#how-it-works">How it works</a></li>
                <li><a class="hover:text-white transition" href="{{ route('contact') }}">Contact us</a></li>
                <li><a class="hover:text-white transition" href="{{ route('register') }}">Join TripCash</a></li>
            </ul>
        </div>

        {{-- Legal --}}
        <div>
            <h4 class="text-white font-semibold text-sm">Legal</h4>
            <ul class="mt-4 space-y-2.5 text-sm">
                <li><a class="hover:text-white transition" href="{{ route('terms') }}">Terms &amp; Conditions</a></li>
                <li><a class="hover:text-white transition" href="{{ route('privacy') }}">Privacy Policy</a></li>
                <li><a class="hover:text-white transition" href="{{ route('refund') }}">Cashback &amp; Refund</a></li>
            </ul>

            <h4 class="text-white font-semibold text-sm mt-6">Support</h4>
            <ul class="mt-3 space-y-2.5 text-sm">
                <li><a class="hover:text-white transition flex items-center gap-2" href="{{ route('contact') }}"><i data-lucide="mail" class="w-3.5 h-3.5 opacity-50"></i> help@tripcash.in</a></li>
            </ul>
        </div>
    </div>

    {{-- Bottom bar --}}
    <div class="border-t border-white/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-slate-400">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved. Made with <span class="text-red-400">&hearts;</span> in India.</p>
            <div class="flex items-center gap-4 text-xs">
                <span class="flex items-center gap-1.5"><i data-lucide="shield-check" class="w-3.5 h-3.5 text-green-400"></i> Secure payments</span>
                <span class="flex items-center gap-1.5"><i data-lucide="wallet" class="w-3.5 h-3.5 text-blue-400"></i> UPI withdrawals</span>
                <span class="flex items-center gap-1.5"><i data-lucide="zap" class="w-3.5 h-3.5 text-yellow-400"></i> Instant tracking</span>
            </div>
        </div>
    </div>
</footer>
