<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name').' — Travel more. Earn cashback.')</title>
    <meta name="description" content="{{ \App\Models\Setting::get('seo.meta_description', 'Compare flights, hotels, trains, cabs & packages and earn real cashback.') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">

    @include('partials.tailwind')
    @include('partials.styles')
    @include('partials.pwa')
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/intersect@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    @stack('head')
</head>
<body class="bg-bg text-ink antialiased font-sans selection:bg-primary/20 pb-safe">
    <x-marketing-nav />
    <x-mobile-app-header />

    <main>
        @yield('content')
    </main>

    <x-marketing-footer />
    <x-bottom-nav />

    <script>
        // Render Lucide icons after DOM + Alpine paints.
        document.addEventListener('DOMContentLoaded', () => window.lucide?.createIcons());
        document.addEventListener('alpine:initialized', () => window.lucide?.createIcons());
    </script>
    @stack('scripts')
</body>
</html>
