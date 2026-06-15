{{-- App-style bottom navigation — mobile only (PWA feel). --}}
@php
    $isAuth = auth()->check();
    $items = [
        ['label' => 'Home', 'icon' => 'home', 'href' => route('home'), 'active' => request()->routeIs('home')],
        ['label' => 'Search', 'icon' => 'search', 'href' => route('search', ['category' => 'hotels']), 'active' => request()->routeIs('search')],
        ['label' => 'Wallet', 'icon' => 'wallet', 'href' => $isAuth ? route('dashboard.wallet') : route('login'), 'active' => request()->routeIs('dashboard.wallet')],
        ['label' => $isAuth ? 'Account' : 'Sign in', 'icon' => 'user', 'href' => $isAuth ? route('dashboard.index') : route('login'), 'active' => request()->routeIs('dashboard.index') || request()->routeIs('dashboard.profile')],
    ];
@endphp
<nav class="bnav md:hidden">
    @foreach ($items as $it)
        <a href="{{ $it['href'] }}" class="{{ $it['active'] ? 'active' : '' }}">
            <span class="bnav-ic"><i data-lucide="{{ $it['icon'] }}" class="w-5 h-5"></i></span>
            {{ $it['label'] }}
        </a>
    @endforeach
</nav>
