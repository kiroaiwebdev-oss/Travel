@extends('layouts.app')
@section('title', 'Enter code — '.config('app.name'))

@section('content')
<div class="max-w-md mx-auto px-4 py-16">
    <div class="card p-8 fade-up">
        <h1 class="font-display text-2xl font-extrabold">Enter your code</h1>
        <p class="text-muted text-sm mt-1">Sent to <span class="font-semibold text-ink">{{ $email }}</span></p>

        @if (session('status'))
            <div class="mt-4 rounded-xl bg-brand/10 text-brand text-sm p-3" style="background:rgba(0,184,169,.1);color:#009688">{{ session('status') }}</div>
        @endif
        @if (session('otp_demo'))
            <div class="mt-3 rounded-xl bg-warning/10 text-warning text-sm p-3">
                <b>Demo mode:</b> your code is <span class="font-mono font-bold">{{ session('otp_demo') }}</span> (production me ye email/SMS pe jayega)
            </div>
        @endif
        @if ($errors->any())
            <div class="mt-3 rounded-xl bg-danger/10 text-danger text-sm p-3">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('login.otp.verify') }}" class="space-y-4 mt-6">
            @csrf
            <input type="hidden" name="email" value="{{ $email }}">
            <div>
                <label class="text-sm font-semibold">6-digit code</label>
                <input name="code" inputmode="numeric" pattern="[0-9]*" maxlength="6" required autofocus
                       class="input mt-1 text-center text-2xl font-bold tracking-[0.4em] font-mono" placeholder="••••••">
            </div>
            <button class="btn btn-primary w-full justify-center">Verify &amp; sign in</button>
        </form>

        <form method="POST" action="{{ route('login.otp.send') }}" class="mt-4 text-center">
            @csrf
            <input type="hidden" name="email" value="{{ $email }}">
            <button class="text-sm text-muted hover:text-ink">Resend code</button>
        </form>
    </div>
</div>
@endsection
