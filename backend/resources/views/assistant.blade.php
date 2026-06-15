@extends('layouts.app')
@section('title', $assistantName.' — AI Travel Assistant — '.config('app.name'))

@section('content')
<section class="hero-aurora border-b border-slate-100">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 pt-8 pb-5 text-center">
        <span class="pill pill-brand"><i data-lucide="sparkles" class="w-3.5 h-3.5"></i> AI-Powered</span>
        <h1 class="mt-3 font-display text-2xl sm:text-4xl font-extrabold">{{ $assistantName }}</h1>
        <p class="text-muted mt-2 text-sm sm:text-base">Find the cheapest flights, the best cashback hotels, or plan a full itinerary — grounded in live TripCash deals.</p>
    </div>
</section>

@if (! $enabled)
    <div class="max-w-3xl mx-auto px-4 sm:px-6 py-16 text-center">
        <div class="card p-10">
            <div class="mx-auto w-14 h-14 grid place-items-center rounded-2xl bg-slate-100 text-muted mb-4"><i data-lucide="bot-off" class="w-7 h-7"></i></div>
            <h2 class="font-display font-bold text-lg">Assistant is currently offline</h2>
            <p class="text-muted mt-1">Our AI assistant is taking a short break. Please check back soon.</p>
            <a href="{{ route('search', ['category' => 'hotels']) }}" class="btn btn-primary mt-5">Browse deals instead <i data-lucide="arrow-right" class="w-4 h-4"></i></a>
        </div>
    </div>
@else
<div class="max-w-3xl mx-auto px-4 sm:px-6 py-6"
     x-data="assistantChat()" x-init="init()">

    {{-- Category grounding selector --}}
    <div class="flex items-center gap-2 mb-3 overflow-x-auto no-scrollbar">
        <span class="text-xs font-semibold text-muted shrink-0">Focus:</span>
        <button @click="category=''" class="pill shrink-0" :class="category==='' ? 'pill-brand' : 'pill-muted'">Any</button>
        @foreach ($categories as $key => $cat)
            <button @click="category='{{ $key }}'" class="pill shrink-0" :class="category==='{{ $key }}' ? 'pill-brand' : 'pill-muted'">{{ $cat['label'] }}</button>
        @endforeach
    </div>

    {{-- Chat window --}}
    <div class="card flex flex-col" style="height:min(68vh,620px)">
        <div x-ref="scroll" class="flex-1 overflow-y-auto p-4 space-y-4">
            <template x-for="(m, i) in msgs" :key="i">
                <div class="flex gap-2.5" :class="m.role==='user' ? 'flex-row-reverse' : ''">
                    <span class="w-8 h-8 rounded-full grid place-items-center shrink-0 text-white"
                          :style="m.role==='user' ? 'background:#0F62FE' : 'background:linear-gradient(150deg,#9333ea,#0F62FE)'">
                        <i :data-lucide="m.role==='user' ? 'user' : 'sparkles'" class="w-4 h-4"></i>
                    </span>
                    <div class="max-w-[80%]">
                        <div class="card px-3.5 py-2.5 text-sm leading-relaxed" style="white-space:pre-line"
                             :class="m.role==='user' ? 'bg-blue-50 border-blue-100' : 'bg-white'"
                             x-text="m.content"></div>
                        <p x-show="m.provider" class="text-[10px] text-muted mt-1 px-1" x-text="'via ' + m.provider"></p>
                    </div>
                </div>
            </template>

            {{-- typing indicator --}}
            <div x-show="loading" class="flex gap-2.5">
                <span class="w-8 h-8 rounded-full grid place-items-center shrink-0 text-white" style="background:linear-gradient(150deg,#9333ea,#0F62FE)"><i data-lucide="sparkles" class="w-4 h-4"></i></span>
                <div class="card px-4 py-3 bg-white flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-slate-300 animate-bounce" style="animation-delay:0ms"></span>
                    <span class="w-2 h-2 rounded-full bg-slate-300 animate-bounce" style="animation-delay:150ms"></span>
                    <span class="w-2 h-2 rounded-full bg-slate-300 animate-bounce" style="animation-delay:300ms"></span>
                </div>
            </div>
        </div>

        {{-- Suggestions --}}
        <div class="px-4 pb-2 flex flex-wrap gap-2" x-show="suggestions.length && !loading">
            <template x-for="(s, i) in suggestions" :key="i">
                <button @click="ask(s)" class="press text-xs font-semibold px-3 py-1.5 rounded-full bg-slate-100 hover:bg-slate-200 transition" x-text="s"></button>
            </template>
        </div>

        {{-- Input --}}
        <form @submit.prevent="ask()" class="border-t border-slate-100 p-3 flex items-end gap-2">
            <textarea x-model="input" rows="1" @keydown.enter.prevent="ask()"
                      placeholder="Ask anything… e.g. cheapest Goa hotel with cashback"
                      class="input flex-1 resize-none max-h-28"></textarea>
            <button type="submit" class="btn btn-primary h-[46px] px-4 shrink-0" :disabled="loading">
                <i data-lucide="send" class="w-4 h-4"></i>
            </button>
        </form>
    </div>

    <p class="text-center text-xs text-muted mt-3">AI can make mistakes — always confirm prices & details on the provider site before booking.</p>
</div>
@endif

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('assistantChat', () => ({
            msgs: [],
            input: '',
            loading: false,
            category: @json(request('category', '')),
            welcome: @json($welcome),
            suggestions: @json($suggestions ?: []),
            init() {
                if (this.welcome) this.msgs.push({ role: 'assistant', content: this.welcome });
                this.$nextTick(() => window.lucide?.createIcons());
            },
            ask(text) {
                const m = (text ?? this.input).trim();
                if (!m || this.loading) return;
                this.msgs.push({ role: 'user', content: m });
                this.input = '';
                this.loading = true;
                this.scrollDown();
                const history = this.msgs.slice(-10).map(x => ({ role: x.role, content: x.content }));
                fetch(@json(url('/api/v1/ai/assistant')), {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ message: m, category: this.category || null, history })
                })
                .then(r => r.json().then(j => ({ ok: r.ok, j })))
                .then(({ ok, j }) => {
                    this.msgs.push({ role: 'assistant', content: j.message || 'Sorry, I could not respond right now.', provider: j.provider_used || null });
                    if (Array.isArray(j.suggestions) && j.suggestions.length) this.suggestions = j.suggestions;
                })
                .catch(() => this.msgs.push({ role: 'assistant', content: 'The AI service is unreachable right now. Please try again in a moment.' }))
                .finally(() => { this.loading = false; this.scrollDown(); this.$nextTick(() => window.lucide?.createIcons()); });
            },
            scrollDown() {
                this.$nextTick(() => { const el = this.$refs.scroll; if (el) el.scrollTop = el.scrollHeight; });
            }
        }));
    });
</script>
@endpush
@endsection
