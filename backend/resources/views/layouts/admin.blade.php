<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — {{ config('app.name') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    @include('partials.tailwind')
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
</head>
<body class="bg-bg text-ink font-sans antialiased">
<div x-data="{ open:false }" class="min-h-screen lg:flex">
    <aside class="fixed lg:static inset-y-0 left-0 z-40 w-64 bg-secondary text-slate-300 p-4 flex flex-col transition-transform lg:translate-x-0"
           :class="open ? 'translate-x-0' : '-translate-x-full'">
        <div class="flex items-center gap-2 font-display font-extrabold text-lg text-white px-2 py-2">
            <span class="grid place-items-center w-8 h-8 rounded-lg bg-primary"><i data-lucide="shield" class="w-4 h-4"></i></span>
            Admin
        </div>
        <nav class="mt-4 space-y-1 flex-1 overflow-y-auto text-sm">
            @php $nav = [
                ['admin.dashboard','layout-dashboard','Dashboard', null],
                ['admin.analytics','bar-chart-3','Analytics', 'analytics.view'],
                ['admin.providers.index','plug','Providers', 'providers.manage'],
                ['admin.cashback-rules.index','badge-percent','Cashback rules', 'cashback.manage'],
                ['admin.users.index','users','Users', 'users.view'],
                ['admin.withdrawals.index','banknote','Withdrawals', 'withdrawals.approve'],
                ['admin.settings.index','settings','Settings', 'settings.manage'],
            ]; @endphp
            @foreach ($nav as [$route, $icon, $label, $perm])
                @if (!$perm || auth()->user()->hasPermission($perm))
                    <a href="{{ route($route) }}"
                       class="flex items-center gap-3 px-3 py-2 rounded-lg transition {{ request()->routeIs(str_replace('.index','*',$route)) || request()->routeIs($route) ? 'bg-white/10 text-white' : 'hover:bg-white/5 hover:text-white' }}">
                        <i data-lucide="{{ $icon }}" class="w-[18px] h-[18px]"></i> {{ $label }}
                    </a>
                @endif
            @endforeach
        </nav>
        <a href="{{ route('home') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-white/5 hover:text-white text-sm"><i data-lucide="external-link" class="w-[18px] h-[18px]"></i> Visit site</a>
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
