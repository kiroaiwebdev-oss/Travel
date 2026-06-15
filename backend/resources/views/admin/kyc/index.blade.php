@extends('layouts.admin')
@section('title', 'KYC Review')
@section('heading', 'KYC Review')

@section('content')
<div class="flex gap-2 mb-5">
    @foreach (['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected', 'none' => 'Not submitted'] as $s => $label)
        <a href="{{ route('admin.kyc.index', ['status' => $s]) }}" class="btn {{ $status===$s ? 'btn-dark' : 'btn-ghost' }} text-sm">{{ $label }}</a>
    @endforeach
</div>

<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="text-left text-muted border-b border-slate-100">
                <tr><th class="p-4 font-semibold">User</th><th class="p-4 font-semibold">Full name</th><th class="p-4 font-semibold">PAN</th><th class="p-4 font-semibold">Method</th><th class="p-4 font-semibold">Submitted</th><th class="p-4 font-semibold text-right">Action</th></tr>
            </thead>
            <tbody>
                @forelse ($users as $u)
                    <tr class="border-b border-slate-50 hover:bg-slate-50">
                        <td class="p-4"><p class="font-medium">{{ $u->name }}</p><p class="text-xs text-muted">{{ $u->email }}</p></td>
                        <td class="p-4">{{ $u->kyc_full_name ?? '—' }}</td>
                        <td class="p-4 font-mono">{{ $u->kyc_pan ?? '—' }}</td>
                        <td class="p-4 uppercase">{{ $u->kyc_payout_method ?? '—' }}</td>
                        <td class="p-4 text-muted">{{ $u->kyc_submitted_at?->format('d M Y') ?? '—' }}</td>
                        <td class="p-4 text-right whitespace-nowrap">
                            @if ($u->kyc_status === 'pending')
                                <form method="POST" action="{{ route('admin.kyc.approve', $u) }}" class="inline">@csrf @method('PUT')<button class="btn btn-ghost text-xs text-success">Approve</button></form>
                                <form method="POST" action="{{ route('admin.kyc.reject', $u) }}" class="inline" onsubmit="this.note.value=prompt('Reason for rejection?')||'';return this.note.value!==''">@csrf @method('PUT')<input type="hidden" name="note"><button class="btn btn-ghost text-xs text-danger">Reject</button></form>
                            @else
                                <span class="pill {{ $u->kyc_status==='approved' ? 'pill-cashback' : 'pill-muted' }}">{{ ucfirst($u->kyc_status) }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6"><x-empty-state icon="id-card" text="No {{ $status }} KYC submissions." /></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4">{{ $users->links() }}</div>
</div>
@endsection
