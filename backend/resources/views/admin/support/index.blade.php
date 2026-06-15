@extends('layouts.admin')
@section('title', 'Support')
@section('heading', 'Support tickets')

@section('content')
<div class="flex gap-2 mb-5">
    @foreach (['' => 'All', 'open' => 'Open', 'pending' => 'Pending', 'resolved' => 'Resolved', 'closed' => 'Closed'] as $s => $label)
        <a href="{{ route('admin.support.index', array_filter(['status' => $s])) }}" class="btn {{ request('status')===$s ? 'btn-dark' : 'btn-ghost' }} text-sm">{{ $label }}</a>
    @endforeach
</div>
<div class="card overflow-hidden">
    <table class="w-full text-sm">
        <thead class="text-left text-muted border-b border-slate-100"><tr><th class="p-4 font-semibold">Ticket</th><th class="p-4 font-semibold">User</th><th class="p-4 font-semibold">Priority</th><th class="p-4 font-semibold">Status</th><th class="p-4 font-semibold">Updated</th></tr></thead>
        <tbody>
            @forelse ($tickets as $t)
                <tr class="border-b border-slate-50 hover:bg-slate-50 cursor-pointer" onclick="window.location='{{ route('admin.support.show', $t) }}'">
                    <td class="p-4"><p class="font-medium">{{ $t->subject }}</p><p class="text-xs text-muted">{{ $t->ticket_number }}</p></td>
                    <td class="p-4">{{ $t->user?->name }}</td>
                    <td class="p-4"><span class="pill pill-muted">{{ ucfirst($t->priority) }}</span></td>
                    <td class="p-4"><span class="pill {{ $t->status==='resolved' ? 'pill-cashback' : 'pill-muted' }}">{{ ucfirst($t->status) }}</span></td>
                    <td class="p-4 text-muted">{{ $t->last_reply_at?->diffForHumans() ?? $t->created_at->diffForHumans() }}</td>
                </tr>
            @empty
                <tr><td colspan="5"><x-empty-state icon="life-buoy" text="No tickets." /></td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4">{{ $tickets->links() }}</div>
</div>
@endsection
