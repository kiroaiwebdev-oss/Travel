<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
<body class="bg-bg text-ink font-sans antialiased">
<div x-data="{ open: false }" class="min-h-screen lg:flex">
    {{-- Sidebar --}}
    <aside class="fixed lg:static inset-y-0 left-0 z-40 w-64 bg-white border-r border-slate-100 p-4 flex flex-col transition-transform lg:translate-x-0"
           :class="open ? 'translate-x-0' : '-translate-x-full'">
        <a href="{{ route('home') }}" class="flex items-center gap-2 font-display font-extrabold text-lg px-2 py-2">
            <span class="grid place-items-center w-8 h-8 rounded-lg bg-primary text-white"><i data-lucide="plane" class="w-4 h-4"></i></span>
            {{ config('app.name') }}
        </a>
        <nav class="mt-4 space-y-1 flex-1 overflow-y-auto">
            @php $nav = [
                ['dashboard.index','layout-dashboard','Dashboard'],
                ['dashboard.wallet','wallet','Wallet'],
                ['dashboard.cashback','badge-percent','Cashback'],
                ['dashboard.bookings','ticket','Bookings'],
                ['dashboard.saved','heart','Saved & Watchlist'],
                ['dashboard.referrals','users','Referrals'],
                ['dashboard.withdrawals','banknote','Withdrawals'],
                ['dashboard.kyc','shield-check','KYC Verification'],
                ['dashboard.notifications','bell','Notifications'],
                ['dashboard.support','life-buoy','Support'],
                ['dashboard.profile','settings','Profile & Settings'],
            ]; @endphp
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

    <div @click="open=false" x-show="open" class="fixed inset-0 bg-black/30 z-30 lg:hidden"></div>

    {{-- Main --}}
    <div class="flex-1 min-w-0">
        <header class="h-16 bg-white/70 glass border-b border-slate-100 flex items-center justify-between px-4 sm:px-6 sticky top-0 z-20">
            <button @click="open=!open" class="lg:hidden btn btn-ghost p-2"><i data-lucide="menu" class="w-5 h-5"></i></button>
            <h1 class="font-display font-bold">@yield('heading', 'Dashboard')</h1>
            <a href="{{ route('home') }}" class="btn btn-ghost text-sm"><i data-lucide="search" class="w-4 h-4"></i> New search</a>
        </header>

        <main class="p-4 sm:p-6 fade-up pb-safe">
            @if (session('status'))
                <div class="mb-4 rounded-xl bg-success/10 text-success text-sm p-3 flex items-center gap-2">
                    <i data-lucide="check-circle" class="w-4 h-4"></i> {{ session('status') }}
                </div>
            @endif
            @yield('content')
        </main>
    </div>
</div>
<x-bottom-nav />
<script>document.addEventListener('DOMContentLoaded',()=>window.lucide?.createIcons());document.addEventListener('alpine:initialized',()=>window.lucide?.createIcons());</script>
@stack('scripts')
</body>
</html>
