@props(['categories', 'active' => 'hotels'])

{{-- Premium multi-tab search. Submits a GET to /search; pure HTML works without JS,
     Alpine just powers the tab switching + field visibility. --}}
<div x-data="{ tab: '{{ $active }}' }" class="card p-2 sm:p-3 shadow-lift">
    <div class="flex gap-1 overflow-x-auto p-1 mb-2">
        @foreach ($categories as $key => $cat)
            <button type="button" @click="tab = '{{ $key }}'"
                    class="flex items-center gap-2 px-3.5 py-2 rounded-xl text-sm font-semibold transition whitespace-nowrap"
                    :class="tab === '{{ $key }}' ? 'bg-primary text-white shadow' : 'text-muted hover:bg-slate-100'">
                <i data-lucide="{{ $cat['icon'] }}" class="w-4 h-4"></i> {{ $cat['label'] }}
            </button>
        @endforeach
    </div>

    @foreach ($categories as $key => $cat)
        <form x-show="tab === '{{ $key }}'" x-cloak method="GET" action="{{ route('search') }}"
              class="grid gap-2 md:grid-cols-[1fr_1fr_auto_auto_auto] items-end p-1">
            <input type="hidden" name="category" value="{{ $key }}">

            @if (in_array($key, ['flights', 'trains', 'cabs', 'transfers']))
                <div>
                    <label class="text-xs font-semibold text-muted px-1">From</label>
                    <input name="origin" class="input" placeholder="Origin" value="{{ request('origin') }}">
                </div>
            @else
                <div class="md:col-span-1">
                    <label class="text-xs font-semibold text-muted px-1">Destination / City</label>
                    <input name="destination" class="input" placeholder="Where to?" value="{{ request('destination') }}">
                </div>
            @endif

            @if (in_array($key, ['flights', 'trains', 'cabs', 'transfers']))
                <div>
                    <label class="text-xs font-semibold text-muted px-1">To</label>
                    <input name="destination" class="input" placeholder="Destination" value="{{ request('destination') }}">
                </div>
            @else
                <div>
                    <label class="text-xs font-semibold text-muted px-1">Check-in</label>
                    <input type="date" name="depart_date" class="input">
                </div>
            @endif

            <div>
                <label class="text-xs font-semibold text-muted px-1">{{ in_array($key, ['flights','trains']) ? 'Depart' : 'Date' }}</label>
                <input type="date" name="{{ in_array($key,['flights','trains','cabs','transfers']) ? 'depart_date' : 'return_date' }}" class="input">
            </div>

            <div>
                <label class="text-xs font-semibold text-muted px-1">Guests</label>
                <input type="number" name="travellers" min="1" max="20" value="1" class="input w-24">
            </div>

            <button type="submit" class="btn btn-primary h-[42px] justify-center">
                <i data-lucide="search" class="w-4 h-4"></i> Search
            </button>
        </form>
    @endforeach
</div>
