<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — {{ config('app.name') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    @include('partials.tailwind')
    @include('partials.styles')
    @include('partials.pwa')
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
</head>
<body class="bg-bg text-ink font-sans antialiased">
<div x-data="{ open:false }" class="min-h-screen lg:flex">
    <aside class="fixed lg:static inset-y-0 left-0 z-40 w-64 text-slate-300 flex flex-col transition-transform lg:translate-x-0"
           style="background:linear-gradient(180deg,#0B1220 0%,#0a2230 55%,#062a2b 100%)"
           :class="open ? 'translate-x-0' : '-translate-x-full'">
        {{-- Brand --}}
        <div class="px-4 pt-5 pb-3 border-b border-white/5">
            <div class="flex items-center">
                <x-brand-logo light icon="shield" />
            </div>
            <div class="mt-3 flex items-center gap-2 rounded-xl bg-white/5 px-3 py-2">
                <span class="w-7 h-7 rounded-full grid place-items-center text-white text-xs font-bold" style="background:linear-gradient(150deg,#0F62FE,#00B8A9)">{{ strtoupper(substr(auth()->user()->name,0,1)) }}</span>
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-white truncate">{{ auth()->user()->name }}</p>
                    <p class="text-[10px] text-slate-400 truncate">{{ auth()->user()->roles->pluck('label')->join(', ') ?: 'Admin' }}</p>
                </div>
            </div>
        </div>

        <nav class="mt-2 px-3 space-y-0.5 flex-1 overflow-y-auto text-sm pb-4">
            @php
                $groups = [
                    'Overview' => [
                        ['admin.dashboard','layout-dashboard','Dashboard', null],
                        ['admin.analytics','bar-chart-3','Analytics', 'analytics.view'],
                    ],
                    'Catalog' => [
                        ['admin.offers.index','tag','Offers & Deals', 'cms.manage'],
                        ['admin.providers.index','plug','Providers', 'providers.manage'],
                        ['admin.networks.index','network','Affiliate Networks', 'providers.manage'],
                        ['admin.cashback-rules.index','badge-percent','Cashback rules', 'cashback.manage'],
                    ],
                    'Finance' => [
                        ['admin.bookings.index','ticket','Bookings', 'analytics.view'],
                        ['admin.cashbacks.index','badge-dollar-sign','Cashback ledger', 'cashback.manage'],
                        ['admin.withdrawals.index','banknote','Withdrawals & Payouts', 'withdrawals.approve'],
                    ],
                    'Users' => [
                        ['admin.users.index','users','Users', 'users.view'],
                        ['admin.staff.index','user-cog','Staff & Roles', 'users.manage'],
                        ['admin.kyc.index','id-card','KYC Review', 'users.manage'],
                    ],
                    'Engage' => [
                        ['admin.support.index','life-buoy','Support', 'support.handle'],
                        ['admin.contact.index','mail','Contact messages', 'support.handle'],
                        ['admin.notifications.index','megaphone','Notifications', 'cms.manage'],
                    ],
                    'System' => [
                        ['admin.audit.index','scroll-text','Audit logs', 'audit.view'],
                        ['admin.integrations.index','plug-zap','Integrations', 'settings.manage'],
                        ['admin.settings.index','settings','Settings', 'settings.manage'],
                    ],
                ];
                $u = auth()->user();
            @endphp

            @foreach ($groups as $section => $items)
                @php $visible = collect($items)->filter(fn ($it) => ! $it[3] || $u->hasPermission($it[3])); @endphp
                @if ($visible->isNotEmpty())
                    <p class="px-3 pt-4 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-500">{{ $section }}</p>
                    @foreach ($visible as [$route, $icon, $label, $perm])
                        @php
                            $pattern = \Illuminate\Support\Str::endsWith($route, '.index') ? \Illuminate\Support\Str::replaceLast('.index', '.*', $route) : $route;
                            $active = request()->routeIs($pattern) || request()->routeIs($route);
                        @endphp
                        <a href="{{ route($route) }}"
                           class="flex items-center gap-3 px-3 py-2 rounded-lg transition text-[13.5px] {{ $active ? 'text-white font-semibold' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}"
                           @if ($active) style="background:linear-gradient(90deg,rgba(0,184,169,.22),rgba(15,98,254,.14)); box-shadow:inset 2px 0 0 #2dd4cb" @endif>
                            <i data-lucide="{{ $icon }}" class="w-[18px] h-[18px] {{ $active ? '' : 'opacity-80' }}"></i> {{ $label }}
                        </a>
                    @endforeach
                @endif
            @endforeach
        </nav>

        <div class="px-3 py-3 border-t border-white/5 space-y-0.5">
            <a href="{{ route('home') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-white/5 hover:text-white text-[13.5px] text-slate-400"><i data-lucide="external-link" class="w-[18px] h-[18px]"></i> Visit site</a>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button class="w-full flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-danger/15 hover:text-white text-[13.5px] text-slate-400 text-left"><i data-lucide="log-out" class="w-[18px] h-[18px]"></i> Sign out</button>
            </form>
        </div>
    </aside>

    <div @click="open=false" x-show="open" class="fixed inset-0 bg-black/40 z-30 lg:hidden"></div>

    <div class="flex-1 min-w-0">
        <header class="h-16 bg-white border-b border-slate-100 flex items-center justify-between px-4 sm:px-6 sticky top-0 z-20">
            <button @click="open=!open" class="lg:hidden btn btn-ghost p-2"><i data-lucide="menu" class="w-5 h-5"></i></button>
            <h1 class="font-display font-bold">@yield('heading', 'Admin')</h1>
            <div class="flex items-center gap-2 text-sm text-muted">
                <i data-lucide="user-circle" class="w-5 h-5"></i> {{ auth()->user()->name }}
            </div>
        </header>
        <main class="p-4 sm:p-6 fade-up">
            @if (session('status'))
                <div class="mb-4 rounded-xl bg-success/10 text-success text-sm p-3 flex items-center gap-2"><i data-lucide="check-circle" class="w-4 h-4"></i> {{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="mb-4 rounded-xl bg-danger/10 text-danger text-sm p-3">{{ $errors->first() }}</div>
            @endif
            @yield('content')
        </main>
    </div>
</div>
<script>document.addEventListener('DOMContentLoaded',()=>window.lucide?.createIcons());document.addEventListener('alpine:initialized',()=>window.lucide?.createIcons());</script>
@stack('scripts')
</body>
</html>
