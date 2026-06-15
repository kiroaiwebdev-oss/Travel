<footer class="mt-24 bg-secondary text-slate-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-14 grid gap-10 md:grid-cols-4">
        <div class="md:col-span-1">
            <div class="flex items-center gap-2 text-white font-display font-extrabold text-lg">
                <span class="grid place-items-center w-9 h-9 rounded-xl text-white" style="background:linear-gradient(150deg,#14b8a6,#0d9488)">
                    <i data-lucide="plane" class="w-5 h-5"></i>
                </span>
                Travel<span style="color:#2dd4cb">Cash</span>
            </div>
            <p class="mt-4 text-sm text-slate-400 max-w-xs">{{ \App\Models\Setting::get('site.tagline', 'Travel more. Pay less. Earn cashback on every trip.') }}</p>
        </div>

        <div>
            <h4 class="text-white font-semibold text-sm">Explore</h4>
            <ul class="mt-4 space-y-2 text-sm">
                <li><a class="hover:text-white transition" href="{{ route('search', ['category' => 'hotels']) }}">Hotels</a></li>
                <li><a class="hover:text-white transition" href="{{ route('search', ['category' => 'flights']) }}">Flights</a></li>
                <li><a class="hover:text-white transition" href="{{ route('search', ['category' => 'trains']) }}">Trains</a></li>
                <li><a class="hover:text-white transition" href="{{ route('search', ['category' => 'packages']) }}">Packages</a></li>
            </ul>
        </div>

        <div>
            <h4 class="text-white font-semibold text-sm">Company</h4>
            <ul class="mt-4 space-y-2 text-sm">
                <li><a class="hover:text-white transition" href="{{ route('about') }}">About us</a></li>
                <li><a class="hover:text-white transition" href="#cashback">How it works</a></li>
                <li><a class="hover:text-white transition" href="{{ route('contact') }}">Contact</a></li>
            </ul>
        </div>

        <div>
            <h4 class="text-white font-semibold text-sm">Legal</h4>
            <ul class="mt-4 space-y-2 text-sm">
                <li><a class="hover:text-white transition" href="{{ route('terms') }}">Terms &amp; Conditions</a></li>
                <li><a class="hover:text-white transition" href="{{ route('privacy') }}">Privacy Policy</a></li>
                <li><a class="hover:text-white transition" href="{{ route('refund') }}">Cashback &amp; Refund</a></li>
            </ul>
        </div>
    </div>
    <div class="border-t border-white/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6 flex flex-col sm:flex-row items-center justify-between gap-3 text-sm text-slate-400">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            <div class="flex items-center gap-4">
                <a href="#" class="hover:text-white"><i data-lucide="twitter" class="w-4 h-4"></i></a>
                <a href="#" class="hover:text-white"><i data-lucide="instagram" class="w-4 h-4"></i></a>
                <a href="#" class="hover:text-white"><i data-lucide="linkedin" class="w-4 h-4"></i></a>
            </div>
        </div>
    </div>
</footer>
