@extends('layouts.app')
@section('title', $title.' — '.config('app.name'))

@section('content')
<section class="hero-aurora">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 pt-16 pb-8 text-center">
        <h1 class="font-display text-3xl sm:text-4xl font-extrabold">{{ $title }}</h1>
        @isset($subtitle)<p class="text-muted mt-2">{{ $subtitle }}</p>@endisset
    </div>
</section>

<section class="max-w-3xl mx-auto px-4 sm:px-6 pb-16">
    <div class="card p-6 sm:p-8 space-y-6">
        @foreach ($sections as [$heading, $body])
            <div>
                <h2 class="font-display font-bold text-lg">{{ $heading }}</h2>
                <p class="text-muted mt-1.5 leading-relaxed">{{ $body }}</p>
            </div>
        @endforeach
        <div class="pt-4 border-t border-slate-100 text-sm text-muted">
            Questions? <a href="{{ route('contact') }}" class="text-pay font-semibold">Contact us</a>.
        </div>
    </div>
</section>
@endsection
