@extends('layouts.app')
@section('title', 'Sign in with OTP — '.config('app.name'))

@section('content')
<div class="max-w-md mx-auto px-4 py-16">
    <div class="card p-8 fade-up">
        <div class="w-11 h-11 rounded-2xl grid place-items-center text-white mb-4" style="background:linear-gradient(150deg,#0F62FE,#00B8A9)">
            <i data-lucide="mail" class="w-5 h-5"></i>
        </div>
        <h1 class="font-display text-2xl font-extrabold">Sign in with a code</h1>
        <p class="text-muted text-sm mt-1">We'll email you a 6-digit one-time code.</p>

        @if ($errors->any())
            <div class="mt-4 rounded-xl bg-danger/10 text-danger text-sm p-3">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('login.otp.send') }}" class="space-y-4 mt-6">
            @csrf
            <div>
                <label class="text-sm font-semibold">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus class="input mt-1" placeholder="you@example.com">
            </div>
            <button class="btn btn-primary w-full justify-center"><i data-lucide="send" class="w-4 h-4"></i> Send code</button>
        </form>

        <p class="text-sm text-muted text-center mt-6">
            Prefer password? <a href="{{ route('login') }}" class="text-pay font-semibold">Sign in normally</a>
        </p>
    </div>
</div>
@endsection
