{{-- Reusable city autocomplete field. Props: name, placeholder, icon, value. --}}
<div x-data="cityField(@js($value ?? ''))" class="relative mt-1">
    <i data-lucide="{{ $icon ?? 'map-pin' }}" class="w-4 h-4 text-muted absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none z-10"></i>
    <input type="text" name="{{ $name }}" autocomplete="off" x-model="q"
           @input="filter()" @focus="filter(); open = true" @keydown.escape="open = false"
           @blur="setTimeout(() => open = false, 150)"
           class="input pl-9" placeholder="{{ $placeholder }}">
    <ul x-show="open && results.length" x-cloak
        class="absolute left-0 right-0 mt-1 bg-white rounded-xl shadow-lg border border-slate-100 max-h-56 overflow-y-auto z-50 py-1">
        <template x-for="(c, idx) in results" :key="idx">
            <li @mousedown.prevent="pick(c)" class="px-3.5 py-2 text-sm hover:bg-slate-50 cursor-pointer flex items-center gap-2">
                <svg class="w-3.5 h-3.5 text-brand shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                <span x-text="c"></span>
            </li>
        </template>
    </ul>
</div>

@once
@push('scripts')
<script>
    window.TC_CITIES = @json(config('cities.list', []));
    document.addEventListener('alpine:init', () => {
        Alpine.data('cityField', (initial) => ({
            q: initial || '',
            open: false,
            results: [],
            filter() {
                const all = window.TC_CITIES || [];
                const t = this.q.trim().toLowerCase();
                this.results = (t === ''
                    ? all
                    : all.filter(c => c.toLowerCase().includes(t))
                ).slice(0, 8);
            },
            pick(c) { this.q = c; this.open = false; },
        }));
    });
</script>
@endpush
@endonce
