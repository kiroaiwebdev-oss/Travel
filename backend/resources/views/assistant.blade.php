@extends('layouts.app')
@section('title', $assistantName.' — AI Travel Assistant — '.config('app.name'))

@section('content')
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
<div class="max-w-3xl mx-auto px-4 sm:px-6 py-5 sm:py-7"
     x-data="assistantChat()">

    {{-- ===== Premium header ===== --}}
    <div class="rounded-3xl p-5 sm:p-6 text-white relative overflow-hidden mb-4" style="background:linear-gradient(135deg,#7c3aed 0%,#0F62FE 100%)">
        <div class="absolute -right-10 -top-10 w-44 h-44 rounded-full blur-3xl" style="background:rgba(255,255,255,.18)"></div>
        <div class="relative flex items-center gap-3">
            <span class="relative grid place-items-center w-12 h-12 rounded-2xl shrink-0" style="background:rgba(255,255,255,.18)">
                <i data-lucide="sparkles" class="w-6 h-6"></i>
            </span>
            <div class="min-w-0">
                <h1 class="font-display font-extrabold text-xl sm:text-2xl leading-tight">{{ $assistantName }}</h1>
                <p class="text-white/85 text-xs sm:text-sm flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-green-400 inline-block"></span>
                    Online · guides hotels, flights, trains, cabs &amp; packages
                </p>
            </div>
        </div>
    </div>

    {{-- ===== Capability chips ===== --}}
    <div class="flex gap-2 overflow-x-auto no-scrollbar mb-3 pb-1">
        @foreach ([
            ['Hotels', 'bed', 'hotels', 'Find the best cashback hotels in Goa under ₹6000 a night'],
            ['Flights', 'plane', 'flights', 'Cheapest Delhi to Dubai flight with good cashback'],
            ['Trains', 'train-front', 'trains', 'Trains from Delhi to Mumbai this weekend'],
            ['Cabs', 'car', 'cabs', 'Book an airport transfer cab in Bengaluru'],
            ['Packages', 'map', 'packages', 'Best 5-day Thailand family package with cashback'],
            ['Plan a trip', 'route', '', 'Plan a 3-day North Goa itinerary with cashback hotels'],
        ] as $c)
            <button @click="quick('{{ $c[2] }}', @js($c[3]))"
                    class="press shrink-0 flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white border border-slate-200 hover:border-brand/50 text-sm font-semibold transition">
                <i data-lucide="{{ $c[1] }}" class="w-4 h-4 text-brand"></i> {{ $c[0] }}
            </button>
        @endforeach
    </div>

    {{-- ===== Chat window ===== --}}
    <div class="card flex flex-col overflow-hidden" style="height:min(72vh,660px)">
        <div x-ref="scroll" class="flex-1 overflow-y-auto p-4 space-y-4 bg-slate-50/40">
            <template x-for="(m, i) in msgs" :key="i">
                <div>
                    <div class="flex gap-2.5" :class="m.role==='user' ? 'flex-row-reverse' : ''">
                        <span class="w-8 h-8 rounded-full grid place-items-center shrink-0 text-white"
                              :style="m.role==='user' ? 'background:#0F62FE' : 'background:linear-gradient(150deg,#9333ea,#0F62FE)'">
                            <i :data-lucide="m.role==='user' ? 'user' : 'sparkles'" class="w-4 h-4"></i>
                        </span>
                        <div class="max-w-[82%]">
                            <div class="px-4 py-2.5 text-sm leading-relaxed rounded-2xl shadow-sm" style="white-space:pre-line"
                                 :class="m.role==='user' ? 'bg-pay text-white rounded-tr-sm' : 'bg-white border border-slate-100 rounded-tl-sm'"
                                 x-text="m.content"></div>
                            <p x-show="m.provider" class="text-[10px] text-muted mt-1 px-1 flex items-center gap-1">
                                <i data-lucide="sparkles" class="w-3 h-3"></i> <span x-text="'powered by ' + m.provider"></span>
                            </p>
                        </div>
                    </div>

                    {{-- Real offer cards with affiliate links --}}
                    <div x-show="m.offers && m.offers.length" class="mt-2 ml-10 space-y-2">
                        <template x-for="(o, oi) in (m.offers || [])" :key="oi">
                            <a :href="o.go_url || '#'" target="_blank" rel="nofollow sponsored"
                               class="press flex gap-3 items-center bg-white border border-slate-100 rounded-xl p-2.5 hover:border-brand/50 transition shadow-sm">
                                <img :src="o.image" x-show="o.image" class="w-14 h-14 rounded-lg object-cover shrink-0" loading="lazy" alt="">
                                <div class="min-w-0 flex-1">
                                    <p class="font-semibold text-sm truncate" x-text="o.title"></p>
                                    <p class="text-xs text-muted truncate" x-text="o.provider_name + (o.rating ? ' · ' + o.rating + '★' : '')"></p>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <span class="font-bold text-sm" x-text="(o.currency==='INR'?'₹':'$') + Math.round(o.price || 0).toLocaleString()"></span>
                                        <span x-show="o.cashback" class="pill pill-cashback text-[10px]" x-text="'₹' + Math.round(o.cashback || 0) + ' cashback'"></span>
                                    </div>
                                </div>
                                <span class="btn btn-primary text-xs shrink-0">Book &amp; earn</span>
                            </a>
                        </template>
                    </div>
                </div>
            </template>

            {{-- typing indicator --}}
            <div x-show="loading" class="flex gap-2.5">
                <span class="w-8 h-8 rounded-full grid place-items-center shrink-0 text-white" style="background:linear-gradient(150deg,#9333ea,#0F62FE)"><i data-lucide="sparkles" class="w-4 h-4"></i></span>
                <div class="bg-white border border-slate-100 rounded-2xl rounded-tl-sm px-4 py-3 flex items-center gap-1 shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-slate-300 animate-bounce" style="animation-delay:0ms"></span>
                    <span class="w-2 h-2 rounded-full bg-slate-300 animate-bounce" style="animation-delay:150ms"></span>
                    <span class="w-2 h-2 rounded-full bg-slate-300 animate-bounce" style="animation-delay:300ms"></span>
                </div>
            </div>
        </div>

        {{-- Suggestions --}}
        <div class="px-3 pt-2.5 flex flex-wrap gap-2 border-t border-slate-100 bg-white" x-show="suggestions.length && !loading" x-cloak>
            <template x-for="(s, i) in suggestions" :key="i">
                <button @click="ask(s)" class="press text-xs font-semibold px-3 py-1.5 rounded-full bg-slate-100 hover:bg-slate-200 transition" x-text="s"></button>
            </template>
        </div>

        {{-- Input --}}
        <form @submit.prevent="ask()" class="border-t border-slate-100 p-3 flex items-end gap-2 bg-white">
            <textarea x-model="input" rows="1" @keydown.enter.prevent="ask()"
                      placeholder="Ask anything… e.g. cheapest Goa hotel with cashback"
                      class="input flex-1 resize-none max-h-28"></textarea>
            <button type="submit" class="btn btn-primary h-[46px] px-4 shrink-0" :disabled="loading" style="background:linear-gradient(180deg,#a855f7,#7c3aed)">
                <i data-lucide="send" class="w-4 h-4"></i>
            </button>
        </form>
    </div>

    <p class="text-center text-xs text-muted mt-3">AI can make mistakes — confirm prices &amp; details on the provider site before booking.</p>
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
            quick(cat, prompt) {
                this.category = cat || '';
                this.ask(prompt);
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
                    this.msgs.push({ role: 'assistant', content: j.message || 'Sorry, I could not respond right now.', provider: j.provider_used || null, offers: Array.isArray(j.offers) ? j.offers : [] });
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
