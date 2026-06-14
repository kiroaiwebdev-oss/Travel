@extends('layouts.app')
@section('title', 'Create account — '.config('app.name'))

@section('content')
<div class="max-w-md mx-auto px-4 py-16">
    <div class="card p-8 fade-up">
        <h1 class="font-display text-2xl font-extrabold">Create your free account</h1>
        <p class="text-muted text-sm mt-1">Start earning cashback on every booking.</p>

        @if ($errors->any())
            <div class="mt-4 rounded-xl bg-danger/10 text-danger text-sm p-3">
                <ul class="list-disc list-inside">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <a href="{{ route('auth.google') }}" class="btn w-full justify-center mt-6 border border-slate-200 bg-white hover:bg-slate-50">
            <img src="https://www.google.com/favicon.ico" class="w-4 h-4" alt=""> Continue with Google
        </a>
        <div class="flex items-center gap-3 my-5 text-xs text-muted"><span class="flex-1 h-px bg-slate-200"></span>or<span class="flex-1 h-px bg-slate-200"></span></div>

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf
            <div>
                <label class="text-sm font-semibold">Full name</label>
                <input name="name" value="{{ old('name') }}" required class="input mt-1" placeholder="Your name">
            </div>
            <div>
                <label class="text-sm font-semibold">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required class="input mt-1" placeholder="you@example.com">
            </div>
            <div>
                <label class="text-sm font-semibold">Password</label>
                <input type="password" name="password" required class="input mt-1" placeholder="At least 8 characters">
            </div>
            <div>
                <label class="text-sm font-semibold">Confirm password</label>
                <input type="password" name="password_confirmation" required class="input mt-1">
            </div>
            <input type="hidden" name="ref" value="{{ $ref ?? request('ref') }}">
            <button class="btn btn-primary w-full justify-center">Create account</button>
        </form>
        <p class="text-sm text-muted text-center mt-6">Already have an account? <a href="{{ route('login') }}" class="text-primary font-semibold">Sign in</a></p>
    </div>
</div>
@endsection
