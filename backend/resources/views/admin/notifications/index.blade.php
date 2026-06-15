@extends('layouts.admin')
@section('title', 'Notifications')
@section('heading', 'Push & Notifications')

@section('content')
<div class="grid lg:grid-cols-3 gap-6">
    <form method="POST" action="{{ route('admin.notifications.send') }}" class="card p-6 space-y-4 lg:col-span-1">
        @csrf
        <h2 class="font-display font-bold">Send a notification</h2>
        <p class="text-sm text-muted">Reaches {{ number_format($userCount) }} active users (in-app instantly).</p>
        <div><label class="text-sm font-semibold">Title</label><input name="title" class="input mt-1" required></div>
        <div><label class="text-sm font-semibold">Message</label><textarea name="body" rows="3" class="input mt-1" required></textarea></div>
        <div class="grid grid-cols-2 gap-3">
            <div><label class="text-sm font-semibold">Category</label>
                <select name="category" class="input mt-1"><option value="general">General</option><option value="promo">Promo</option><option value="cashback">Cashback</option><option value="system">System</option></select>
            </div>
            <div><label class="text-sm font-semibold">Audience</label>
                <select name="audience" class="input mt-1"><option value="all">All users</option><option value="kyc_approved">KYC approved</option><option value="with_balance">With balance</option></select>
            </div>
        </div>
        <div><label class="text-sm font-semibold">Link (optional)</label><input name="url" class="input mt-1" placeholder="/dashboard/wallet"></div>
        <button class="btn btn-primary w-full justify-center"><i data-lucide="send" class="w-4 h-4"></i> Send notification</button>
        <p class="text-xs text-muted">
            Browser web-push: {!! $webpushReady ? '<span class="text-success font-semibold">configured</span>' : '<span class="text-warning font-semibold">add VAPID keys to enable</span>' !!}
        </p>
    </form>

    <div class="card overflow-hidden lg:col-span-2">
        <h2 class="font-display font-bold p-5 pb-0">Recent broadcasts</h2>
        <div class="divide-y divide-slate-100 mt-3">
            @forelse ($recent as $n)
                @php $d = json_decode($n->data, true) ?? []; @endphp
                <div class="p-4">
                    <div class="flex items-center justify-between">
                        <p class="font-semibold">{{ $d['title'] ?? 'Notification' }}</p>
                        <span class="text-xs text-muted">{{ \Illuminate\Support\Carbon::parse($n->created_at)->diffForHumans() }}</span>
                    </div>
                    <p class="text-sm text-muted mt-0.5">{{ $d['message'] ?? '' }}</p>
                </div>
            @empty
                <x-empty-state icon="megaphone" text="No notifications sent yet." />
            @endforelse
        </div>
    </div>
</div>
@endsection
