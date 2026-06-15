@extends('layouts.app')
@section('title', $title.' — '.config('app.name'))

@section('content')
<section class="hero-aurora border-b border-slate-100">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 pt-10 pb-8 text-center">
        <span class="pill pill-brand mx-auto"><i data-lucide="{{ $icon ?? 'file-text' }}" class="w-3.5 h-3.5"></i> {{ $eyebrow ?? 'Legal' }}</span>
        <h1 class="mt-4 font-display text-3xl sm:text-4xl font-extrabold">{{ $title }}</h1>
        @isset($subtitle)<p class="text-muted mt-2">{{ $subtitle }}</p>@endisset
    </div>
</section>

<section class="max-w-5xl mx-auto px-4 sm:px-6 py-10">
    <div class="lg:grid lg:grid-cols-[220px_1fr] lg:gap-10">
        {{-- Table of contents (desktop) --}}
        <aside class="hidden lg:block">
            <nav class="sticky top-24 space-y-0.5">
                <p class="text-[11px] font-bold uppercase tracking-wider text-muted mb-2 px-3">On this page</p>
                @foreach ($sections as $i => $section)
                    <a href="#sec-{{ $i }}" class="block px-3 py-1.5 rounded-lg text-sm text-muted hover:bg-slate-100 hover:text-ink transition">{{ $section[0] }}</a>
                @endforeach
            </nav>
        </aside>

        <div>
            @isset($intro)
                <div class="card p-5 sm:p-6 mb-5 bg-slate-50 border-slate-200">
                    <p class="text-muted leading-relaxed">{{ $intro }}</p>
                </div>
            @endisset

            <div class="space-y-4">
                @foreach ($sections as $i => $section)
                    @php [$heading, $body] = $section; @endphp
                    <div id="sec-{{ $i }}" class="card p-6 scroll-mt-24">
                        <div class="flex items-start gap-3">
                            <span class="w-8 h-8 rounded-lg bg-pay/10 text-pay grid place-items-center font-bold text-sm shrink-0">{{ $i + 1 }}</span>
                            <div class="min-w-0">
                                <h2 class="font-display font-bold text-lg">{{ $heading }}</h2>
                                @if (is_array($body))
                                    <ul class="mt-2.5 space-y-2">
                                        @foreach ($body as $point)
                                            <li class="flex gap-2.5 text-muted text-sm leading-relaxed">
                                                <i data-lucide="check-circle" class="w-4 h-4 text-brand shrink-0 mt-0.5"></i><span>{{ $point }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-muted mt-1.5 leading-relaxed">{{ $body }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Contact CTA --}}
            <div class="card p-6 mt-6 flex flex-col sm:flex-row items-center justify-between gap-3 text-white" style="background:linear-gradient(120deg,#0d9488,#0F62FE)">
                <div class="text-center sm:text-left">
                    <p class="font-display font-bold text-lg">Still have questions?</p>
                    <p class="text-white/85 text-sm">Our support team is happy to help with anything.</p>
                </div>
                <a href="{{ route('contact') }}" class="btn btn-white shrink-0">Contact us <i data-lucide="arrow-right" class="w-4 h-4"></i></a>
            </div>
        </div>
    </div>
</section>
@endsection
