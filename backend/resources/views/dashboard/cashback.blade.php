@extends('layouts.dashboard')
@section('title', 'Cashback')
@section('heading', 'Cashback')

@section('content')
<div class="grid gap-4 sm:grid-cols-4">
    @foreach ([['Pending','pending','warning'],['Confirmed','confirmed','primary'],['Withdrawable','withdrawable','success'],['Rejected','rejected','danger']] as [$label,$key,$color])
        <div class="card p-5">
            <p class="text-sm text-muted">{{ $label }}</p>
            <p class="mt-2 text-2xl font-extrabold font-display text-{{ $color }}">₹{{ number_format($totals[$key] ?? 0, 2) }}</p>
        </div>
    @endforeach
</div>

<div class="card mt-6 overflow-hidden">
    <h2 class="font-display font-bold p-5 pb-0">Cashback history</h2>
    <div class="overflow-x-auto mt-3">
        <table class="w-full text-sm">
            <thead class="text-left text-muted border-b border-slate-100">
                <tr><th class="p-4 font-semibold">Provider</th><th class="p-4 font-semibold">Category</th><th class="p-4 font-semibold text-right">Booking</th><th class="p-4 font-semibold text-right">Cashback</th><th class="p-4 font-semibold">Status</th><th class="p-4 font-semibold">Matures</th></tr>
            </thead>
            <tbody>
                @forelse ($cashbacks as $cb)
                    <tr class="border-b border-slate-50 hover:bg-slate-50">
                        <td class="p-4 font-medium">{{ $cb->provider?->name ?? '—' }}</td>
                        <td class="p-4">{{ ucfirst($cb->category) }}</td>
                        <td class="p-4 text-right text-muted">₹{{ number_format($cb->booking_amount, 0) }}</td>
                        <td class="p-4 text-right font-semibold text-success">₹{{ number_format($cb->amount, 2) }}</td>
                        <td class="p-4">
                            @php $map=['pending'=>'pill-muted','confirmed'=>'pill-muted','withdrawable'=>'pill-cashback','paid'=>'pill-cashback','rejected'=>'pill-muted']; @endphp
                            <span class="pill {{ $map[$cb->status] ?? 'pill-muted' }}">{{ ucfirst($cb->status) }}</span>
                        </td>
                        <td class="p-4 text-muted">{{ $cb->matures_at?->format('d M Y') ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6"><x-empty-state icon="badge-percent" text="No cashback yet. Book through TravelCash to start earning." /></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4">{{ $cashbacks->links() }}</div>
</div>
@endsection
