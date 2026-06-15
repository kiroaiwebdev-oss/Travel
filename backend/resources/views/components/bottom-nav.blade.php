{{-- App-style bottom navigation — mobile only (native PWA feel) with a center search FAB. --}}
@php
    $isAuth = auth()->check();
    $left = [
        ['label' => 'Home', 'icon' => 'home', 'href' => route('home'), 'active' => request()->routeIs('home')],
        ['label' => 'Explore', 'icon' => 'compass', 'href' => route('search', ['category' => 'packages']), 'active' => request()->routeIs('search') && request('category') === 'packages'],
    ];
    $right = [
        ['label' => 'Wallet', 'icon' => 'wallet', 'href' => $isAuth ? route('dashboard.wallet') : route('login'), 'active' => request()->routeIs('dashboard.wallet')],
        ['label' => $isAuth ? 'Account' : 'Sign in', 'icon' => 'user', 'href' => $isAuth ? route('dashboard.index') : route('login'), 'active' => request()->routeIs('dashboard.index') || request()->routeIs('dashboard.profile')],
    ];
@endphp
<nav class="bnav md:hidden">
    @foreach ($left as $it)
        <a href="{{ $it['href'] }}" class="press {{ $it['active'] ? 'active' : '' }}">
            <span class="bnav-ic"><i data-lucide="{{ $it['icon'] }}" class="w-5 h-5"></i></span>
            {{ $it['label'] }}
        </a>
    @endforeach

    {{-- Center search FAB --}}
    <div class="bnav-fab" style="flex:1">
        <a href="{{ route('search', ['category' => 'hotels']) }}" class="press {{ request()->routeIs('search') ? 'active' : '' }}" aria-label="Search">
            <i data-lucide="search" class="w-6 h-6"></i>
        </a>
    </div>

    @foreach ($right as $it)
        <a href="{{ $it['href'] }}" class="press {{ $it['active'] ? 'active' : '' }}">
            <span class="bnav-ic"><i data-lucide="{{ $it['icon'] }}" class="w-5 h-5"></i></span>
            {{ $it['label'] }}
        </a>
    @endforeach
</nav>
