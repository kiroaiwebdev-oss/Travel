{{-- Native-app style top header — MOBILE ONLY (md:hidden).
     On the home/landing route it uses a gradient hero header; elsewhere a clean white header. --}}
@php
    $isAuth = auth()->check();
    $isHome = request()->routeIs('home');
    $user = auth()->user();
    $firstName = $isAuth ? strtok($user->name ?? 'there', ' ') : null;
    $hour = (int) now()->format('G');
    $greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
@endphp

<header class="md:hidden app-header {{ $isHome ? 'app-header-gradient' : '' }}">
    <div class="px-4 py-3 flex items-center justify-between {{ $isHome ? 'text-white' : '' }}">
        {{-- Left: greeting (home) or logo --}}
        @if ($isHome)
            <div class="min-w-0">
                <p class="text-[11px] font-medium opacity-80 leading-none">{{ $isAuth ? $greeting.',' : 'Welcome to' }}</p>
                <p class="font-display font-extrabold text-lg leading-tight truncate">
                    {{ $isAuth ? $firstName.' 👋' : 'TravelCash' }}
                </p>
            </div>
        @else
            <a href="{{ route('home') }}" class="flex items-center gap-2 font-display font-extrabold text-base">
                <span class="grid place-items-center w-8 h-8 rounded-xl text-white" style="background:linear-gradient(150deg,#14b8a6,#0d9488)">
                    <i data-lucide="plane" class="w-4 h-4"></i>
                </span>
                Travel<span class="text-brand">Cash</span>
            </a>
        @endif

        {{-- Right: wallet pill + notifications --}}
        <div class="flex items-center gap-2">
            @auth
                <a href="{{ route('dashboard.wallet') }}"
                   class="press flex items-center gap-1.5 px-2.5 py-1.5 rounded-full font-bold text-xs
                          {{ $isHome ? 'bg-white/15 text-white' : 'bg-green-50 text-green-700 border border-green-100' }}">
                    <i data-lucide="wallet" class="w-3.5 h-3.5"></i>
                    ₹{{ number_format((float) ($user->wallet?->balance ?? 0), 0) }}
                </a>
                <a href="{{ route('dashboard.notifications') }}"
                   class="press relative grid place-items-center w-9 h-9 rounded-full {{ $isHome ? 'bg-white/15 text-white' : 'bg-slate-100 text-ink' }}">
                    <i data-lucide="bell" class="w-4 h-4"></i>
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-red-500 ring-2 {{ $isHome ? 'ring-[#0f766e]' : 'ring-white' }}"></span>
                </a>
            @else
                <a href="{{ route('login') }}"
                   class="press px-3.5 py-1.5 rounded-full font-bold text-xs
                          {{ $isHome ? 'bg-white text-ink' : 'bg-pay text-white' }}">
                    Sign in
                </a>
            @endauth
        </div>
    </div>
</header>
