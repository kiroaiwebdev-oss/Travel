@extends('layouts.admin')
@section('title', 'Analytics')
@section('heading', 'Analytics')

@section('content')
<div class="grid gap-5 lg:grid-cols-2">
    <div class="card p-5">
        <h2 class="font-display font-bold mb-4">Searches (last 14 days)</h2>
        @php $max = max(1, $searchesByDay->max() ?? 1); @endphp
        <div class="flex items-end gap-1.5 h-40">
            @forelse ($searchesByDay as $day => $count)
                <div class="flex-1 flex flex-col items-center justify-end group">
                    <span class="text-[10px] text-muted opacity-0 group-hover:opacity-100">{{ $count }}</span>
                    <div class="w-full rounded-t bg-primary/80 hover:bg-primary transition" style="height: {{ max(4, ($count / $max) * 140) }}px"></div>
                    <span class="text-[9px] text-muted mt-1">{{ \Illuminate\Support\Carbon::parse($day)->format('d/m') }}</span>
                </div>
            @empty
                <p class="text-sm text-muted">No search data yet.</p>
            @endforelse
        </div>
    </div>

    <div class="card p-5">
        <h2 class="font-display font-bold mb-4">Top categories (30 days)</h2>
        @php $cmax = max(1, $topCategories->max() ?? 1); @endphp
        <div class="space-y-3">
            @forelse ($topCategories as $cat => $count)
                <div>
                    <div class="flex justify-between text-sm mb-1"><span class="capitalize">{{ $cat }}</span><span class="text-muted">{{ $count }}</span></div>
                    <div class="h-2 rounded-full bg-slate-100"><div class="h-2 rounded-full bg-accent" style="width: {{ ($count / $cmax) * 100 }}%"></div></div>
                </div>
            @empty
                <p class="text-sm text-muted">No data yet.</p>
            @endforelse
        </div>
    </div>
</div>

<div class="card p-5 mt-5">
    <h2 class="font-display font-bold mb-3">Revenue by provider</h2>
    <table class="w-full text-sm">
        <thead class="text-left text-muted border-b border-slate-100"><tr><th class="py-2">Provider</th><th class="py-2 text-right">Commission</th></tr></thead>
        <tbody>
            @forelse ($revenueByProvider as $row)
                <tr class="border-b border-slate-50"><td class="py-2.5">{{ $row->provider?->name ?? '—' }}</td><td class="py-2.5 text-right font-semibold">₹{{ number_format($row->commission, 2) }}</td></tr>
            @empty
                <tr><td colspan="2" class="py-4 text-muted text-center">No revenue yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
