<header x-data="{ open: false, scrolled: false }" @scroll.window="scrolled = window.scrollY > 8"
        class="sticky top-0 z-50 transition-all"
        :class="scrolled ? 'glass shadow-soft' : 'bg-transparent'">
    <nav class="max-w-7xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
        <a href="{{ route('home') }}" class="flex items-center gap-2 font-display font-extrabold text-lg">
            <span class="grid place-items-center w-9 h-9 rounded-xl bg-primary text-white shadow-lift">
                <i data-lucide="plane" class="w-5 h-5"></i>
            </span>
            <span>{{ config('app.name') }}</span>
        </a>

        <div class="hidden md:flex items-center gap-1">
            <a href="{{ route('search', ['category' => 'hotels']) }}" class="btn btn-ghost text-sm">Hotels</a>
            <a href="{{ route('search', ['category' => 'flights']) }}" class="btn btn-ghost text-sm">Flights</a>
            <a href="{{ route('search', ['category' => 'packages']) }}" class="btn btn-ghost text-sm">Packages</a>
            <a href="#cashback" class="btn btn-ghost text-sm">How cashback works</a>
        </div>

        <div class="flex items-center gap-2">
            @auth
                <a href="{{ route('dashboard.index') }}" class="btn btn-ghost text-sm hidden sm:inline-flex">Dashboard</a>
                <a href="{{ route('dashboard.wallet') }}" class="btn btn-dark text-sm">
                    <i data-lucide="wallet" class="w-4 h-4"></i> Wallet
                </a>
            @else
                <a href="{{ route('login') }}" class="btn btn-ghost text-sm">Sign in</a>
                <a href="{{ route('register') }}" class="btn btn-primary text-sm">Get started</a>
            @endauth
            <button @click="open = !open" class="md:hidden btn btn-ghost p-2" aria-label="Menu">
                <i data-lucide="menu" class="w-5 h-5"></i>
            </button>
        </div>
    </nav>

    <div x-show="open" x-collapse class="md:hidden border-t border-slate-100 bg-white px-4 py-3 space-y-1">
        <a href="{{ route('search', ['category' => 'hotels']) }}" class="nav-link">Hotels</a>
        <a href="{{ route('search', ['category' => 'flights']) }}" class="nav-link">Flights</a>
        <a href="{{ route('search', ['category' => 'packages']) }}" class="nav-link">Packages</a>
    </div>
</header>
