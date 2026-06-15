@extends('layouts.admin')
@section('title', 'Contact messages')
@section('heading', 'Contact messages')

@section('content')
<div class="flex gap-2 mb-5">
    @foreach (['' => 'All', 'new' => 'New ('.$newCount.')', 'replied' => 'Replied', 'closed' => 'Closed'] as $s => $label)
        <a href="{{ route('admin.contact.index', array_filter(['status' => $s])) }}" class="btn {{ request('status')===$s ? 'btn-dark' : 'btn-ghost' }} text-sm">{{ $label }}</a>
    @endforeach
</div>

<div class="card overflow-hidden">
    <table class="w-full text-sm">
        <thead class="text-left text-muted border-b border-slate-100"><tr><th class="p-4 font-semibold">From</th><th class="p-4 font-semibold">Subject</th><th class="p-4 font-semibold">Status</th><th class="p-4 font-semibold">When</th></tr></thead>
        <tbody>
            @forelse ($messages as $m)
                <tr class="border-b border-slate-50 hover:bg-slate-50 cursor-pointer" onclick="window.location='{{ route('admin.contact.show', $m) }}'">
                    <td class="p-4"><p class="font-medium">{{ $m->name }}</p><p class="text-xs text-muted">{{ $m->email }}</p></td>
                    <td class="p-4">{{ $m->subject }}</td>
                    <td class="p-4"><span class="pill {{ $m->status==='new' ? 'pill-deal' : ($m->status==='replied' ? 'pill-cashback' : 'pill-muted') }}">{{ ucfirst($m->status) }}</span></td>
                    <td class="p-4 text-muted">{{ $m->created_at->diffForHumans() }}</td>
                </tr>
            @empty
                <tr><td colspan="4"><x-empty-state icon="mail" text="No contact messages." /></td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4">{{ $messages->links() }}</div>
</div>
@endsection
