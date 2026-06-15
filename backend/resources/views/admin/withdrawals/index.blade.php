@extends('layouts.admin')
@section('title', 'Withdrawals')
@section('heading', 'Withdrawals & Payouts')

@section('content')
<div class="flex flex-wrap gap-2 mb-5 items-center">
    @foreach (['' => 'All', 'requested' => 'Requested', 'processing' => 'Processing', 'paid' => 'Paid', 'rejected' => 'Rejected'] as $s => $label)
        <a href="{{ route('admin.withdrawals.index', array_filter(['status' => $s])) }}" class="btn {{ request('status')===$s ? 'btn-dark' : 'btn-ghost' }} text-sm">{{ $label }}</a>
    @endforeach
    <span class="ml-auto text-xs text-muted">
        Gateways:
        @foreach ($gateways as $g => $ready)
            <span class="pill {{ $ready ? 'pill-cashback' : 'pill-muted' }} ml-1">{{ $g }} {{ $ready ? '✓' : '—' }}</span>
        @endforeach
    </span>
</div>

<div class="card overflow-hidden">
    <table class="w-full text-sm">
        <thead class="text-left text-muted border-b border-slate-100"><tr><th class="p-4 font-semibold">User</th><th class="p-4 font-semibold">Method</th><th class="p-4 font-semibold text-right">Amount</th><th class="p-4 font-semibold">Status</th><th class="p-4 font-semibold">Ref</th><th class="p-4 font-semibold text-right">Action</th></tr></thead>
        <tbody>
            @forelse ($withdrawals as $w)
                <tr class="border-b border-slate-50 align-top">
                    <td class="p-4"><p class="font-medium">{{ $w->user->name }}</p><p class="text-xs text-muted">{{ $w->user->email }}</p></td>
                    <td class="p-4 uppercase">{{ $w->method }}</td>
                    <td class="p-4 text-right font-semibold">₹{{ number_format($w->amount, 2) }}</td>
                    <td class="p-4"><span class="pill {{ $w->status==='paid' ? 'pill-cashback' : 'pill-muted' }}">{{ ucfirst($w->status) }}</span></td>
                    <td class="p-4 text-xs text-muted">{{ $w->gateway_payout_id ?? $w->reference ?? '—' }}</td>
                    <td class="p-4 text-right">
                        @if (in_array($w->status, ['requested','approved']))
                            <form method="POST" action="{{ route('admin.withdrawals.process', $w) }}" class="inline-flex items-center gap-1">
                                @csrf @method('PUT')
                                <select name="gateway" class="input py-1.5 text-xs w-28">
                                    <option value="manual">Manual</option>
                                    <option value="razorpay">Razorpay</option>
                                    <option value="paypal">PayPal</option>
                                </select>
                                <button class="btn btn-primary text-xs">Pay out</button>
                            </form>
                            <form method="POST" action="{{ route('admin.withdrawals.reject', $w) }}" class="inline" onsubmit="this.admin_note.value=prompt('Reason?')||'';return this.admin_note.value!==''">@csrf @method('PUT')<input type="hidden" name="admin_note"><button class="btn btn-ghost text-xs text-danger">Reject</button></form>
                        @elseif ($w->status === 'processing')
                            <form method="POST" action="{{ route('admin.withdrawals.approve', $w) }}" class="inline">@csrf @method('PUT')<button class="btn btn-ghost text-xs text-success">Mark paid</button></form>
                        @else
                            <span class="text-xs text-muted">{{ $w->admin_note ?? '—' }}</span>
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
