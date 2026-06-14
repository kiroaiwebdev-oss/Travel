@extends('layouts.admin')
@section('title', 'Withdrawals')
@section('heading', 'Withdrawals')

@section('content')
<div class="flex gap-2 mb-5">
    @foreach (['' => 'All', 'requested' => 'Requested', 'paid' => 'Paid', 'rejected' => 'Rejected'] as $s => $label)
        <a href="{{ route('admin.withdrawals.index', ['status' => $s]) }}" class="btn {{ request('status')===$s ? 'btn-dark' : 'btn-ghost' }} text-sm">{{ $label }}</a>
    @endforeach
</div>

<div class="card overflow-hidden">
    <table class="w-full text-sm">
        <thead class="text-left text-muted border-b border-slate-100"><tr><th class="p-4 font-semibold">User</th><th class="p-4 font-semibold">Method</th><th class="p-4 font-semibold text-right">Amount</th><th class="p-4 font-semibold">Status</th><th class="p-4 font-semibold">Date</th><th class="p-4 font-semibold text-right">Actions</th></tr></thead>
        <tbody>
            @forelse ($withdrawals as $w)
                <tr class="border-b border-slate-50" x-data="{ open:false }">
                    <td class="p-4"><p class="font-medium">{{ $w->user->name }}</p><p class="text-xs text-muted">{{ $w->user->email }}</p></td>
                    <td class="p-4 uppercase">{{ $w->method }}</td>
                    <td class="p-4 text-right font-semibold">₹{{ number_format($w->amount, 2) }}</td>
                    <td class="p-4"><span class="pill {{ $w->status==='paid' ? 'pill-cashback' : 'pill-muted' }}">{{ ucfirst($w->status) }}</span></td>
                    <td class="p-4 text-muted">{{ $w->created_at->format('d M Y') }}</td>
                    <td class="p-4 text-right">
                        @if ($w->status === 'requested')
                            <form method="POST" action="{{ route('admin.withdrawals.approve', $w) }}" class="inline">@csrf @method('PUT')<button class="btn btn-ghost text-xs text-success">Mark paid</button></form>
                            <form method="POST" action="{{ route('admin.withdrawals.reject', $w) }}" class="inline">@csrf @method('PUT')<input type="hidden" name="admin_note" value="Rejected by admin"><button class="btn btn-ghost text-xs text-danger">Reject</button></form>
                        @else
                            <span class="text-xs text-muted">{{ $w->reference ?? $w->admin_note ?? '—' }}</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6"><x-empty-state icon="banknote" text="No withdrawals." /></td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4">{{ $withdrawals->links() }}</div>
</div>
@endsection
