@extends('layouts.admin')
@section('title', 'Reviews & Suggestions')
@section('heading', 'Reviews & Suggestions')

@section('content')
{{-- Status filter tabs --}}
<div class="flex flex-wrap items-center gap-2 mb-5">
    @foreach (['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'] as $k => $lbl)
        <a href="{{ route('admin.reviews.index', ['status' => $k, 'type' => $type]) }}"
           class="pill {{ $status === $k ? 'pill-cashback' : 'pill-muted' }}">{{ $lbl }} ({{ $counts[$k] }})</a>
    @endforeach
    <span class="mx-1 text-slate-300">|</span>
    <a href="{{ route('admin.reviews.index', ['status' => $status]) }}" class="pill {{ ! $type ? 'pill-brand' : 'pill-muted' }}">All</a>
    <a href="{{ route('admin.reviews.index', ['status' => $status, 'type' => 'review']) }}" class="pill {{ $type === 'review' ? 'pill-brand' : 'pill-muted' }}">Reviews</a>
    <a href="{{ route('admin.reviews.index', ['status' => $status, 'type' => 'suggestion']) }}" class="pill {{ $type === 'suggestion' ? 'pill-deal' : 'pill-muted' }}">Suggestions</a>
</div>

<div class="space-y-3">
    @forelse ($reviews as $r)
        <div class="card p-5">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="font-semibold">{{ $r->name }}</span>
                        <span class="pill {{ $r->type === 'suggestion' ? 'pill-deal' : 'pill-brand' }}">{{ ucfirst($r->type) }}</span>
                        @if ($r->type === 'review' && $r->rating)<span class="text-warning text-sm">@for ($i=0;$i<$r->rating;$i++)★@endfor</span>@endif
                        @if ($r->is_featured)<span class="pill pill-cashback">Featured</span>@endif
                    </div>
                    @if ($r->location)<p class="text-xs text-muted mt-0.5">{{ $r->location }}</p>@endif
                    <p class="text-sm text-ink mt-2 leading-relaxed">{{ $r->message }}</p>
                    <p class="text-xs text-muted mt-1">{{ $r->created_at->diffForHumans() }} @if ($r->user) · from registered user @endif</p>
                </div>
                <div class="flex flex-col gap-1.5 shrink-0 w-28">
                    @if ($r->status !== 'approved')
                        <form method="POST" action="{{ route('admin.reviews.approve', $r) }}">@csrf @method('PUT')<button class="btn btn-brand text-xs w-full justify-center">Approve</button></form>
                    @endif
                    @if ($r->status !== 'rejected')
                        <form method="POST" action="{{ route('admin.reviews.reject', $r) }}">@csrf @method('PUT')<button class="btn btn-ghost text-xs w-full justify-center">Reject</button></form>
                    @endif
                    @if ($r->type === 'review' && $r->status === 'approved')
                        <form method="POST" action="{{ route('admin.reviews.feature', $r) }}">@csrf @method('PUT')<button class="btn btn-ghost text-xs w-full justify-center">{{ $r->is_featured ? 'Unfeature' : 'Feature' }}</button></form>
                    @endif
                    <form method="POST" action="{{ route('admin.reviews.destroy', $r) }}" onsubmit="return confirm('Delete permanently?')">@csrf @method('DELETE')<button class="btn btn-ghost text-xs text-danger w-full justify-center">Delete</button></form>
                </div>
            </div>
        </div>
    @empty
        <x-empty-state icon="message-square" text="Nothing here in “{{ $status }}”." />
    @endforelse
</div>

<div class="mt-4">{{ $reviews->links() }}</div>
@endsection
