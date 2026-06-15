<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — {{ config('app.name') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    @include('partials.tailwind')
    @include('partials.styles')
    @include('partials.pwa')
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
</head>
<body class="bg-bg text-ink font-sans antialiased pb-safe">
@php
    $isIndex = request()->routeIs('dashboard.index');
    $nav = [
        ['dashboard.index','layout-dashboard','Dashboard','Overview & activity'],
        ['dashboard.wallet','wallet','Wallet','Balance & transactions'],
        ['dashboard.cashback','badge-percent','Cashback','Track your earnings'],
        ['dashboard.bookings','ticket','Bookings','Your trip history'],
        ['dashboard.saved','heart','Saved & Watchlist','Deals you love'],
        ['dashboard.referrals','users','Referrals','Invite & earn'],
        ['dashboard.withdrawals','banknote','Withdrawals','Cash out to UPI/bank'],
        ['dashboard.kyc','shield-check','KYC Verification','Verify your identity'],
        ['dashboard.notifications','bell','Notifications','Updates & alerts'],
        ['dashboard.support','life-buoy','Support','Get help'],
        ['dashboard.profile','settings','Profile & Settings','Manage your account'],
    ];
@endphp

<div x-data="{ open: false }" class="min-h-screen lg:flex">
    {{-- ===== Desktop sidebar ===== --}}
    <aside class="hidden lg:flex fixed lg:static inset-y-0 left-0 z-40 w-64 bg-white border-r border-slate-100 p-4 flex-col">
        <a href="{{ route('home') }}" class="flex items-center gap-2 font-display font-extrabold text-lg px-2 py-2">
            <span class="grid place-items-center w-8 h-8 rounded-lg bg-primary text-white"><i data-lucide="plane" class="w-4 h-4"></i></span>
            {{ config('app.name') }}
        </a>
        <nav class="mt-4 space-y-1 flex-1 overflow-y-auto">
            @foreach ($nav as [$route, $icon, $label])
                <a href="{{ route($route) }}" class="nav-link {{ request()->routeIs($route) ? 'active' : '' }}">
                    <i data-lucide="{{ $icon }}" class="w-[18px] h-[18px]"></i> {{ $label }}
                </a>
            @endforeach
        </nav>
        <form method="POST" action="{{ route('logout') }}" class="pt-2 border-t border-slate-100">
            @csrf
            <button class="nav-link w-full text-left"><i data-lucide="log-out" class="w-[18px] h-[18px]"></i> Sign out</button>
        </form>
    </aside>

    {{-- ===== Mobile slide-in drawer (full menu) ===== --}}
    <div x-show="open" x-cloak class="lg:hidden">
        <div @click="open=false" x-show="open" x-transition.opacity class="fixed inset-0 bg-black/40 z-[85]"></div>
        <aside class="drawer lg:hidden" x-show="open"
               x-transition:enter="transition ease-out duration-250" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
               x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full">
            <div class="flex items-center justify-between px-2 py-2">
                <a href="{{ route('home') }}" class="flex items-center gap-2 font-display font-extrabold text-lg">
                    <span class="grid place-items-center w-8 h-8 rounded-lg text-white" style="background:linear-gradient(150deg,#14b8a6,#0d9488)"><i data-lucide="plane" class="w-4 h-4"></i></span>
                    Trip<span class="text-brand">Cash</span>
                </a>
                <button @click="open=false" class="app-iconbtn"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>

            {{-- user mini-card --}}
            @auth
            <a href="{{ route('dashboard.profile') }}" class="flex items-center gap-3 p-3 mt-2 rounded-xl bg-slate-50">
                @if (auth()->user()->avatar_url)
                    <img src="{{ auth()->user()->avatar_url }}" class="w-11 h-11 rounded-full object-cover" alt="">
                @else
                    <span class="w-11 h-11 rounded-full grid place-items-center text-white font-bold" style="background:linear-gradient(150deg,#14b8a6,#0d9488)">{{ strtoupper(substr(auth()->user()->name,0,1)) }}</span>
                @endif
                <span class="min-w-0">
                    <span class="block font-semibold text-sm truncate">{{ auth()->user()->name }}</span>
                    <span class="block text-xs text-muted truncate">{{ auth()->user()->email }}</span>
                </span>
            </a>
            @endauth

            <nav class="mt-3 space-y-1">
                @foreach ($nav as [$route, $icon, $label])
                    <a href="{{ route($route) }}" class="nav-link {{ request()->routeIs($route) ? 'active' : '' }}">
                        <i data-lucide="{{ $icon }}" class="w-[18px] h-[18px]"></i> {{ $label }}
                    </a>
                @endforeach
            </nav>
            <form method="POST" action="{{ route('logout') }}" class="mt-3 pt-3 border-t border-slate-100">
                @csrf
                <button class="nav-link w-full text-left text-danger"><i data-lucide="log-out" class="w-[18px] h-[18px]"></i> Sign out</button>
            </form>
        </aside>
    </div>

    {{-- ===== Main ===== --}}
    <div class="flex-1 min-w-0">
        {{-- Desktop header --}}
        <header class="hidden lg:flex h-16 bg-white/70 glass border-b border-slate-100 items-center justify-between px-6 sticky top-0 z-20">
            <h1 class="font-display font-bold">@yield('heading', 'Dashboard')</h1>
            <a href="{{ route('home') }}" class="btn btn-ghost text-sm"><i data-lucide="search" class="w-4 h-4"></i> New search</a>
        </header>

        {{-- Mobile app top bar --}}
        <header class="lg:hidden app-topbar">
            <div class="app-topbar-row">
                @if ($isIndex)
                    <button @click="open=true" class="app-iconbtn" aria-label="Menu"><i data-lucide="menu" class="w-5 h-5"></i></button>
                @else
                    <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('dashboard.index') }}" class="app-iconbtn" aria-label="Back"><i data-lucide="chevron-left" class="w-6 h-6"></i></a>
                @endif
                <h1 class="font-display font-extrabold text-base flex-1 text-center truncate px-1">@yield('heading', 'Dashboard')</h1>
                <a href="{{ route('dashboard.notifications') }}" class="app-iconbtn relative" aria-label="Notifications">
                    <i data-lucide="bell" class="w-5 h-5"></i>
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-red-500 ring-2 ring-white"></span>
                </a>
            </div>
        </header>

        <main class="p-4 sm:p-6 fade-up pb-safe">
            @if (session('status'))
                <div x-data="{ show:true }" x-show="show" x-init="setTimeout(()=>show=false,4000)"
                     class="mb-4 rounded-xl bg-success/10 text-success text-sm p-3 flex items-center gap-2">
                    <i data-lucide="check-circle" class="w-4 h-4"></i> {{ session('status') }}
                </div>
            @endif
            @yield('content')
        </main>
    </div>
</div>

<x-bottom-nav />
<script>
    document.addEventListener('DOMContentLoaded',()=>window.lucide?.createIcons());
    document.addEventListener('alpine:initialized',()=>window.lucide?.createIcons());
</script>
@stack('scripts')
</body>
</html>
