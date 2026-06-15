@extends('layouts.admin')
@section('title', 'Cashback ledger')
@section('heading', 'Cashback ledger')

@section('content')
<div class="grid gap-4 sm:grid-cols-4 mb-5">
    @foreach ([['Pending','pending','warning'],['Confirmed','confirmed','primary'],['Withdrawable','withdrawable','success'],['Rejected','rejected','danger']] as [$label,$key,$c])
        <a href="{{ route('admin.cashbacks.index', ['status' => $key]) }}" class="card p-5 card-hover">
            <p class="text-sm text-muted">{{ $label }}</p>
            <p class="mt-2 text-2xl font-extrabold font-display text-{{ $c }}">₹{{ number_format($totals[$key] ?? 0, 2) }}</p>
        </a>
    @endforeach
</div>

<div class="card overflow-hidden">
    <div class="flex items-center justify-between p-4 pb-0">
        <h2 class="font-display font-bold">Transactions</h2>
        <a href="{{ route('admin.cashbacks.index') }}" class="text-xs text-primary font-semibold">Show all</a>
    </div>
    <div class="overflow-x-auto mt-3">
        <table class="w-full text-sm">
            <thead class="text-left text-muted border-b border-slate-100">
                <tr><th class="p-4 font-semibold">User</th><th class="p-4 font-semibold">Provider</th><th class="p-4 font-semibold text-right">Amount</th><th class="p-4 font-semibold">Status</th><th class="p-4 font-semibold">Matures</th><th class="p-4 font-semibold text-right">Control</th></tr>
            </thead>
            <tbody>
                @forelse ($cashbacks as $cb)
                    <tr class="border-b border-slate-50 hover:bg-slate-50">
                        <td class="p-4"><p class="font-medium">{{ $cb->user?->name }}</p><p class="text-xs text-muted">{{ $cb->user?->email }}</p></td>
                        <td class="p-4">{{ $cb->provider?->name }}</td>
                        <td class="p-4 text-right font-semibold text-success">₹{{ number_format($cb->amount, 2) }}</td>
                        <td class="p-4"><span class="pill pill-muted">{{ ucfirst($cb->status) }}</span></td>
                        <td class="p-4 text-muted">{{ $cb->matures_at?->format('d M Y') ?? '—' }}</td>
                        <td class="p-4 text-right whitespace-nowrap">
                            @if ($cb->status === 'pending')
                                <form method="POST" action="{{ route('admin.cashbacks.confirm', $cb) }}" class="inline">@csrf @method('PUT')<button class="btn btn-ghost text-xs text-success">Confirm</button></form>
                            @elseif ($cb->status === 'confirmed')
                                <form method="POST" action="{{ route('admin.cashbacks.mature', $cb) }}" class="inline">@csrf @method('PUT')<button class="btn btn-ghost text-xs text-success">Release</button></form>
                            @endif
                            @if (!in_array($cb->status, ['rejected','paid']))
                                <form method="POST" action="{{ route('admin.cashbacks.reject', $cb) }}" class="inline">@csrf @method('PUT')<button class="btn btn-ghost text-xs text-danger">Reject</button></form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6"><x-empty-state icon="badge-percent" text="No cashback transactions." /></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4">{{ $cashbacks->links() }}</div>
</div>
@endsection
