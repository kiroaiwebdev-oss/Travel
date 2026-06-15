@extends('layouts.app')
@section('title', 'Contact us — '.config('app.name'))

@section('content')
<section class="hero-aurora border-b border-slate-100">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 pt-10 pb-8 text-center">
        <span class="pill pill-brand mx-auto"><i data-lucide="headset" class="w-3.5 h-3.5"></i> We're here to help</span>
        <h1 class="mt-4 font-display text-3xl sm:text-4xl font-extrabold">Get in touch</h1>
        <p class="text-muted mt-2 max-w-xl mx-auto">Questions about cashback, payouts, KYC or partnerships? Send us a message and our team will reply within 24–48 hours.</p>
    </div>
</section>

<section class="max-w-5xl mx-auto px-4 sm:px-6 py-10">
    <div class="grid lg:grid-cols-[1fr_1.4fr] gap-6">

        {{-- ===== Left: contact info ===== --}}
        <div class="space-y-4">
            <div class="card p-6 space-y-4">
                @foreach ([
                    ['mail', 'Email us', 'support@tripcash.test', 'background:rgba(15,98,254,.1);color:#0F62FE'],
                    ['clock', 'Working hours', 'Mon–Sat · 10 AM – 7 PM IST', 'background:rgba(0,184,169,.12);color:#009688'],
                    ['timer', 'Response time', 'Within 24–48 hours', 'background:rgba(34,197,94,.12);color:#16a34a'],
                    ['map-pin', 'Based in', 'India · serving travellers nationwide', 'background:rgba(255,138,0,.12);color:#c2410c'],
                ] as [$ic, $t, $v, $style])
                    <div class="flex items-center gap-3">
                        <span class="w-11 h-11 rounded-xl grid place-items-center shrink-0" style="{{ $style }}"><i data-lucide="{{ $ic }}" class="w-5 h-5"></i></span>
                        <div>
                            <p class="text-xs text-muted">{{ $t }}</p>
                            <p class="font-semibold text-sm">{{ $v }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="card p-6">
                <p class="font-semibold text-sm mb-3">Follow us</p>
                <div class="flex items-center gap-2">
                    @foreach (['twitter','instagram','linkedin','youtube'] as $s)
                        <a href="#" class="w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 grid place-items-center transition" aria-label="{{ $s }}"><i data-lucide="{{ $s }}" class="w-4 h-4 text-ink"></i></a>
                    @endforeach
                </div>
            </div>
        </div>


        {{-- ===== Right: message form ===== --}}
        <div>
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
                <h2 class="font-display font-bold text-lg">Send us a message</h2>
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-semibold">Name</label>
                        <input name="name" value="{{ old('name', auth()->user()?->name ?? '') }}" class="input mt-1" required>
                    </div>
                    <div>
                        <label class="text-sm font-semibold">Email</label>
                        <input type="email" name="email" value="{{ old('email', auth()->user()?->email ?? '') }}" class="input mt-1" required>
                    </div>
                </div>
                <div>
                    <label class="text-sm font-semibold">Subject</label>
                    <input name="subject" value="{{ old('subject') }}" class="input mt-1" placeholder="e.g. Missing cashback on my Goa booking" required>
                </div>
                <div>
                    <label class="text-sm font-semibold">Message</label>
                    <textarea name="message" rows="5" class="input mt-1" placeholder="Tell us how we can help…" required>{{ old('message') }}</textarea>
                </div>
                <button class="btn btn-primary w-full justify-center text-base py-3"><i data-lucide="send" class="w-4 h-4"></i> Send message</button>
                <p class="text-xs text-muted text-center">We respect your privacy — your details are only used to respond to you.</p>
            </form>
        </div>
    </div>

    {{-- ===== Quick help FAQ ===== --}}
    <div class="mt-10">
        <h2 class="font-display font-bold text-xl text-center">Quick answers</h2>
        <div class="mt-5 grid sm:grid-cols-2 gap-4" x-data="{ open: null }">
            @foreach ([
                ['Where is my cashback?', 'Cashback shows as “pending” within hours of booking and is confirmed after the provider validates it (30–90 days). Track it in your Wallet.'],
                ['How do I withdraw?', 'Complete KYC, then withdraw your confirmed balance to UPI, bank or PayPal from Wallet → Withdrawals.'],
                ['Do I pay more via TripCash?', 'No — you pay the same provider price. Cashback comes from the commission the provider pays us.'],
                ['Partnership / provider enquiry?', 'Use the form above with subject “Partnership” and our team will connect you with the right contact.'],
            ] as $i => $faq)
                <div class="card overflow-hidden">
                    <button @click="open === {{ $i }} ? open = null : open = {{ $i }}" class="w-full flex items-center justify-between p-4 text-left font-semibold text-sm">
                        {{ $faq[0] }}
                        <i data-lucide="chevron-down" class="w-4 h-4 text-muted transition shrink-0" :class="open === {{ $i }} && 'rotate-180'"></i>
                    </button>
                    <div x-show="open === {{ $i }}" x-collapse><p class="px-4 pb-4 text-muted text-sm leading-relaxed">{{ $faq[1] }}</p></div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endsection
