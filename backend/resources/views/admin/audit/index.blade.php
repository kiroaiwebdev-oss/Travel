@extends('layouts.admin')
@section('title', 'Audit logs')
@section('heading', 'Audit logs')

@section('content')
<form method="GET" class="mb-5 flex gap-2 max-w-md">
    <input name="action" value="{{ $q }}" class="input" placeholder="Filter by action (e.g. admin.login)…">
    <button class="btn btn-dark">Filter</button>
</form>

<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="text-left text-muted border-b border-slate-100">
                <tr><th class="p-4 font-semibold">When</th><th class="p-4 font-semibold">Actor</th><th class="p-4 font-semibold">Action</th><th class="p-4 font-semibold">IP</th></tr>
            </thead>
            <tbody>
                @forelse ($logs as $log)
                    <tr class="border-b border-slate-50 hover:bg-slate-50">
                        <td class="p-4 text-muted whitespace-nowrap">{{ $log->created_at?->format('d M Y H:i') }}</td>
                        <td class="p-4">{{ $log->user?->name ?? 'System/Guest' }}</td>
                        <td class="p-4"><span class="font-mono text-xs">{{ $log->action }}</span></td>
                        <td class="p-4 text-muted">{{ $log->ip_address }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4"><x-empty-state icon="scroll-text" text="No audit entries yet." /></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4">{{ $logs->links() }}</div>
</div>
@endsection
