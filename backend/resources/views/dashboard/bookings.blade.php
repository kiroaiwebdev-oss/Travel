@extends('layouts.dashboard')
@section('title', 'Bookings')
@section('heading', 'Bookings')

@section('content')
<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="text-left text-muted border-b border-slate-100">
                <tr><th class="p-4 font-semibold">Booking</th><th class="p-4 font-semibold">Provider</th><th class="p-4 font-semibold">Category</th><th class="p-4 font-semibold text-right">Amount</th><th class="p-4 font-semibold">Status</th><th class="p-4 font-semibold">Date</th></tr>
            </thead>
            <tbody>
                @forelse ($bookings as $b)
                    <tr class="border-b border-slate-50 hover:bg-slate-50">
                        <td class="p-4 font-medium">{{ $b->title ?? '#'.$b->id }}</td>
                        <td class="p-4">{{ $b->provider?->name }}</td>
                        <td class="p-4">{{ ucfirst($b->category) }}</td>
                        <td class="p-4 text-right font-semibold">₹{{ number_format($b->amount, 0) }}</td>
                        <td class="p-4"><span class="pill pill-muted">{{ ucfirst($b->status) }}</span></td>
                        <td class="p-4 text-muted">{{ $b->created_at->format('d M Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6"><x-empty-state icon="ticket" text="No bookings yet. Your trips will appear here." /></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4">{{ $bookings->links() }}</div>
</div>
@endsection
