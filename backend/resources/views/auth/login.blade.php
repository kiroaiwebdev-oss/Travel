@extends('layouts.app')
@section('title', 'Sign in — '.config('app.name'))

@section('content')
<div class="max-w-md mx-auto px-4 py-16">
    <div class="card p-8 fade-up">
        <h1 class="font-display text-2xl font-extrabold">Welcome back</h1>
        <p class="text-muted text-sm mt-1">Sign in to access your wallet and cashback.</p>

        @if ($errors->any())
            <div class="mt-4 rounded-xl bg-danger/10 text-danger text-sm p-3">{{ $errors->first() }}</div>
        @endif

        <a href="{{ route('auth.google') }}" class="btn w-full justify-center mt-6 border border-slate-200 bg-white hover:bg-slate-50">
            <img src="https://www.google.com/favicon.ico" class="w-4 h-4" alt=""> Continue with Google
        </a>
        <a href="{{ route('login.otp') }}" class="btn w-full justify-center mt-2 border border-slate-200 bg-white hover:bg-slate-50">
            <i data-lucide="mail" class="w-4 h-4"></i> Sign in with email OTP
        </a>

        <div class="flex items-center gap-3 my-5 text-xs text-muted"><span class="flex-1 h-px bg-slate-200"></span>or<span class="flex-1 h-px bg-slate-200"></span></div>

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf
            <div>
                <label class="text-sm font-semibold">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus class="input mt-1" placeholder="you@example.com">
            </div>
            <div>
                <label class="text-sm font-semibold">Password</label>
                <input type="password" name="password" required class="input mt-1" placeholder="••••••••">
            </div>
            <label class="flex items-center gap-2 text-sm text-muted">
                <input type="checkbox" name="remember" class="rounded border-slate-300"> Remember me
            </label>
            <button class="btn btn-primary w-full justify-center">Sign in</button>
        </form>

        <p class="text-sm text-muted text-center mt-6">No account? <a href="{{ route('register') }}" class="text-primary font-semibold">Create one</a></p>
    </div>
    <p class="text-xs text-center text-muted mt-4">Demo: admin@travelcash.test / user@travelcash.test · password</p>
</div>
@endsection
