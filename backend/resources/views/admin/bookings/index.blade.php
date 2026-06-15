@extends('layouts.admin')
@section('title', 'Bookings')
@section('heading', 'Bookings')

@section('content')
<div class="flex flex-wrap gap-2 mb-5">
    @foreach (['' => 'All', 'pending' => 'Pending', 'confirmed' => 'Confirmed', 'completed' => 'Completed', 'cancelled' => 'Cancelled'] as $s => $label)
        <a href="{{ route('admin.bookings.index', array_filter(['status' => $s])) }}" class="btn {{ request('status')===$s ? 'btn-dark' : 'btn-ghost' }} text-sm">{{ $label }}</a>
    @endforeach
</div>

<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="text-left text-muted border-b border-slate-100">
                <tr><th class="p-4 font-semibold">#</th><th class="p-4 font-semibold">User</th><th class="p-4 font-semibold">Provider</th><th class="p-4 font-semibold">Category</th><th class="p-4 font-semibold text-right">Amount</th><th class="p-4 font-semibold text-right">Commission</th><th class="p-4 font-semibold">Status</th><th class="p-4 font-semibold text-right">Action</th></tr>
            </thead>
            <tbody>
                @forelse ($bookings as $b)
                    <tr class="border-b border-slate-50 hover:bg-slate-50">
                        <td class="p-4 text-muted">{{ $b->id }}</td>
                        <td class="p-4"><p class="font-medium">{{ $b->user?->name ?? 'Guest' }}</p><p class="text-xs text-muted">{{ $b->user?->email }}</p></td>
                        <td class="p-4">{{ $b->provider?->name }}</td>
                        <td class="p-4">{{ ucfirst($b->category) }}</td>
                        <td class="p-4 text-right font-semibold">₹{{ number_format($b->amount, 0) }}</td>
                        <td class="p-4 text-right text-muted">₹{{ number_format($b->commission_amount, 0) }}</td>
                        <td class="p-4"><span class="pill pill-muted">{{ ucfirst($b->status) }}</span></td>
                        <td class="p-4 text-right">
                            <form method="POST" action="{{ route('admin.bookings.status', $b) }}" class="inline-flex items-center gap-1">
                                @csrf @method('PUT')
                                <select name="status" class="input py-1.5 text-xs w-32">
                                    @foreach (['pending','confirmed','completed','cancelled','refunded'] as $st)
                                        <option value="{{ $st }}" @selected($b->status===$st)>{{ ucfirst($st) }}</option>
                                    @endforeach
                                </select>
                                <button class="btn btn-ghost text-xs">Save</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8"><x-empty-state icon="ticket" text="No bookings yet." /></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4">{{ $bookings->links() }}</div>
</div>
@endsection
