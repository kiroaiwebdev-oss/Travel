<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Sign in — {{ config('app.name') }}</title>
    <meta name="robots" content="noindex,nofollow">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@700;800&display=swap" rel="stylesheet">
    @include('partials.tailwind')
    @include('partials.styles')
    <script defer src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
</head>
<body class="min-h-screen grid place-items-center p-4" style="background:radial-gradient(60% 50% at 50% 0%,rgba(15,98,254,.18),transparent 60%),#0B1220">
    <div class="w-full max-w-md">
        <div class="text-center mb-6">
            <div class="mx-auto w-12 h-12 rounded-2xl grid place-items-center text-white shadow-lift" style="background:linear-gradient(150deg,#0F62FE,#00B8A9)">
                <i data-lucide="shield" class="w-6 h-6"></i>
            </div>
            <h1 class="mt-4 font-display text-2xl font-extrabold text-white">Admin Control Center</h1>
            <p class="text-slate-400 text-sm mt-1">Authorized personnel only.</p>
        </div>

        <div class="card p-7">
            @if ($errors->any())
                <div class="mb-4 rounded-xl bg-danger/10 text-danger text-sm p-3 flex items-center gap-2">
                    <i data-lucide="alert-triangle" class="w-4 h-4 shrink-0"></i> {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login.attempt') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="text-sm font-semibold">Admin email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus class="input mt-1" placeholder="admin@travelcash.test">
                </div>
                <div>
                    <label class="text-sm font-semibold">Password</label>
                    <input type="password" name="password" required class="input mt-1" placeholder="••••••••">
                </div>
                <label class="flex items-center gap-2 text-sm text-muted">
                    <input type="checkbox" name="remember" class="rounded border-slate-300"> Keep me signed in
                </label>
                <button class="btn btn-primary w-full justify-center"><i data-lucide="lock" class="w-4 h-4"></i> Secure sign in</button>
            </form>
        </div>

        <p class="text-center text-xs text-slate-500 mt-5">
            <i data-lucide="shield-check" class="w-3 h-3 inline"></i>
            Protected area · attempts are rate-limited &amp; audited.
            <a href="{{ route('home') }}" class="text-slate-400 underline ml-1">Back to site</a>
        </p>
    </div>
    <script>document.addEventListener('DOMContentLoaded',()=>window.lucide?.createIcons());</script>
</body>
</html>
