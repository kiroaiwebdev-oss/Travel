@extends('layouts.app')
@section('title', 'Contact us — '.config('app.name'))

@section('content')
<section class="hero-aurora">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 pt-16 pb-8 text-center">
        <span class="pill pill-brand mx-auto"><i data-lucide="mail" class="w-3.5 h-3.5"></i> We’re here to help</span>
        <h1 class="mt-4 font-display text-3xl sm:text-4xl font-extrabold">Contact us</h1>
        <p class="text-muted mt-2">Questions about cashback, payouts or partnerships? Send us a message.</p>
    </div>
</section>

<section class="max-w-2xl mx-auto px-4 sm:px-6 pb-16">
    @if (session('status'))
        <div class="mb-4 rounded-xl bg-success/10 text-success text-sm p-3 flex items-center gap-2">
            <i data-lucide="check-circle" class="w-4 h-4"></i> {{ session('status') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="mb-4 rounded-xl bg-danger/10 text-danger text-sm p-3">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('contact.store') }}" class="card p-6 sm:p-8 space-y-4">
        @csrf
        <div class="grid sm:grid-cols-2 gap-4">
            <div><label class="text-sm font-semibold">Name</label><input name="name" value="{{ old('name', auth()->user()->name ?? '') }}" class="input mt-1" required></div>
            <div><label class="text-sm font-semibold">Email</label><input type="email" name="email" value="{{ old('email', auth()->user()->email ?? '') }}" class="input mt-1" required></div>
        </div>
        <div><label class="text-sm font-semibold">Subject</label><input name="subject" value="{{ old('subject') }}" class="input mt-1" required></div>
        <div><label class="text-sm font-semibold">Message</label><textarea name="message" rows="5" class="input mt-1" required>{{ old('message') }}</textarea></div>
        <button class="btn btn-primary w-full justify-center"><i data-lucide="send" class="w-4 h-4"></i> Send message</button>
    </form>

    <div class="grid sm:grid-cols-3 gap-4 mt-6">
        @foreach ([['mail','Email','support@travelcash.test'],['clock','Hours','Mon–Sat, 10–7'],['shield','Response','Within 24–48h']] as [$ic,$t,$v])
            <div class="card p-4 text-center">
                <i data-lucide="{{ $ic }}" class="w-5 h-5 text-brand mx-auto"></i>
                <p class="font-semibold text-sm mt-2">{{ $t }}</p>
                <p class="text-xs text-muted">{{ $v }}</p>
            </div>
        @endforeach
    </div>
</section>
@endsection
