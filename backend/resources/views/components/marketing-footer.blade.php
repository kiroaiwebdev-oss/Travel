<footer class="mt-24 bg-secondary text-slate-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-14 grid gap-10 md:grid-cols-4">
        <div class="md:col-span-1">
            <div class="flex items-center gap-2 text-white font-display font-extrabold text-lg">
                <span class="grid place-items-center w-9 h-9 rounded-xl bg-primary text-white">
                    <i data-lucide="plane" class="w-5 h-5"></i>
                </span>
                {{ config('app.name') }}
            </div>
            <p class="mt-4 text-sm text-slate-400 max-w-xs">{{ \App\Models\Setting::get('site.tagline', 'Travel more. Pay less. Earn cashback on every trip.') }}</p>
        </div>

        @foreach ([
            'Explore' => ['Hotels' => 'hotels', 'Flights' => 'flights', 'Trains' => 'trains', 'Packages' => 'packages'],
            'Company' => ['About' => '#', 'Careers' => '#', 'Blog' => '#', 'Press' => '#'],
            'Support' => ['Help center' => '#', 'Contact' => '#', 'Terms' => '#', 'Privacy' => '#'],
        ] as $heading => $links)
            <div>
                <h4 class="text-white font-semibold text-sm">{{ $heading }}</h4>
                <ul class="mt-4 space-y-2 text-sm">
                    @foreach ($links as $label => $href)
                        <li><a class="hover:text-white transition" href="{{ $href === '#' || !in_array($href, ['hotels','flights','trains','packages']) ? $href : route('search', ['category' => $href]) }}">{{ $label }}</a></li>
                    @endforeach
                </ul>
            </div>
        @endforeach
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
