@props(['categories', 'active' => 'hotels'])

{{-- Premium multi-tab search. App-style on mobile, full multi-field on desktop.
     Pure HTML works without JS; Alpine powers the tabs. --}}
<div x-data="{ tab: '{{ $active }}' }" class="card p-2.5 sm:p-3" style="box-shadow:0 30px 60px -24px rgba(13,42,72,.35)">

    {{-- Tabs: app segmented feel on mobile, pill row on desktop --}}
    <div class="flex gap-1 overflow-x-auto no-scrollbar p-1 mb-2">
        @foreach ($categories as $key => $cat)
            <button type="button" @click="tab = '{{ $key }}'"
                    class="press flex items-center gap-2 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition whitespace-nowrap"
                    :class="tab === '{{ $key }}' ? 'text-white shadow' : 'text-muted hover:bg-slate-100'"
                    :style="tab === '{{ $key }}' ? 'background:linear-gradient(180deg,#14b8a6,#0d9488)' : ''">
                <i data-lucide="{{ $cat['icon'] }}" class="w-4 h-4"></i> {{ $cat['label'] }}
            </button>
        @endforeach
    </div>

    @foreach ($categories as $key => $cat)
        <form x-show="tab === '{{ $key }}'" x-cloak method="GET" action="{{ route('search') }}"
              class="grid gap-2.5 md:grid-cols-[1fr_1fr_auto_auto_auto] md:gap-2 items-end p-1">
            <input type="hidden" name="category" value="{{ $key }}">

            @if (in_array($key, ['flights', 'trains', 'cabs', 'transfers']))
                <div>
                    <label class="text-xs font-semibold text-muted px-1">From</label>
                    <input name="origin" class="input mt-1" placeholder="Origin city" value="{{ request('origin') }}">
                </div>
                <div>
                    <label class="text-xs font-semibold text-muted px-1">To</label>
                    <input name="destination" class="input mt-1" placeholder="Destination" value="{{ request('destination') }}">
                </div>
                <div>
                    <label class="text-xs font-semibold text-muted px-1">Depart</label>
                    <input type="date" name="depart_date" class="input mt-1">
                </div>
            @else
                <div>
                    <label class="text-xs font-semibold text-muted px-1">Destination / City</label>
                    <input name="destination" class="input mt-1" placeholder="Where to?" value="{{ request('destination') }}">
                </div>
                <div>
                    <label class="text-xs font-semibold text-muted px-1">Check-in</label>
                    <input type="date" name="depart_date" class="input mt-1">
                </div>
                <div>
                    <label class="text-xs font-semibold text-muted px-1">Check-out</label>
                    <input type="date" name="return_date" class="input mt-1">
                </div>
            @endif

            <div>
                <label class="text-xs font-semibold text-muted px-1">Guests</label>
                <input type="number" name="travellers" min="1" max="20" value="1" class="input mt-1 md:w-24">
            </div>

            <button type="submit" class="btn btn-brand h-[48px] justify-center text-base md:text-sm">
                <i data-lucide="search" class="w-4 h-4"></i> Search deals
            </button>
        </form>
    @endforeach
</div>
