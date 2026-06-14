@extends('layouts.dashboard')
@section('title', 'Notifications')
@section('heading', 'Notifications')

@section('content')
<div class="card divide-y divide-slate-100">
    @forelse ($notifications as $n)
        <div class="p-4 flex items-start gap-3 {{ $n->read_at ? '' : 'bg-primary/5' }}">
            <span class="grid place-items-center w-9 h-9 rounded-xl bg-slate-100 text-primary shrink-0"><i data-lucide="bell" class="w-4 h-4"></i></span>
            <div class="flex-1">
                <p class="text-sm">{{ $n->data['message'] ?? 'Notification' }}</p>
                <p class="text-xs text-muted mt-0.5">{{ $n->created_at->diffForHumans() }}</p>
            </div>
        </div>
    @empty
        <x-empty-state icon="bell-off" text="You're all caught up. No notifications." />
    @endforelse
</div>
<div class="mt-4">{{ $notifications->links() }}</div>
@endsection
