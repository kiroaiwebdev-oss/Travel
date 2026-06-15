@extends('layouts.dashboard')
@section('title', 'Profile')
@section('heading', 'Profile & Settings')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    {{-- ===== Profile header ===== --}}
    <div class="card p-6 text-center relative overflow-hidden">
        <div class="absolute inset-x-0 top-0 h-20" style="background:linear-gradient(135deg,#0d9488,#0F62FE)"></div>
        <div class="relative">
            <div class="relative inline-block">
                @if ($user->avatar_url)
                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="mx-auto w-24 h-24 rounded-full object-cover ring-4 ring-white shadow-soft">
                @else
                    <div class="mx-auto w-24 h-24 rounded-full grid place-items-center text-white text-3xl font-bold font-display ring-4 ring-white shadow-soft" style="background:linear-gradient(150deg,#14b8a6,#0d9488)">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif
            </div>
            <p class="mt-3 font-display font-extrabold text-lg">{{ $user->name }}</p>
            <p class="text-sm text-muted">{{ $user->email }}</p>
            <span class="pill pill-cashback mt-3 inline-flex">{{ ucfirst($user->status) }}</span>
        </div>
    </div>

    {{-- ===== Change photo ===== --}}
    <div>
        <p class="list-label">Profile photo</p>
        <form method="POST" action="{{ route('dashboard.profile.avatar') }}" enctype="multipart/form-data"
              class="list-group p-4" x-data="{ name: '' }">
            @csrf
            <label class="flex items-center gap-3 cursor-pointer">
                <span class="list-row-ic" style="background:rgba(15,98,254,.1);color:#0F62FE"><i data-lucide="camera" class="w-4 h-4"></i></span>
                <span class="flex-1 min-w-0">
                    <span class="list-row-title block">Upload a new photo</span>
                    <span class="list-row-sub block" x-text="name || 'JPG / PNG / WEBP, max 2MB'"></span>
                </span>
                <input type="file" name="avatar" accept="image/png,image/jpeg,image/webp" required class="hidden"
                       @change="name = $event.target.files[0]?.name">
                <span class="btn btn-ghost text-sm border border-slate-200">Browse</span>
            </label>
            <button class="btn btn-primary w-full justify-center mt-3 text-sm"><i data-lucide="upload" class="w-4 h-4"></i> Upload photo</button>
        </form>
    </div>

    {{-- ===== Account details ===== --}}
    <form method="POST" action="{{ route('dashboard.profile.update') }}">
        @csrf @method('PUT')
        <p class="list-label">Account details</p>
        <div class="list-group p-4 space-y-4">
            <div>
                <label class="field-label text-sm font-semibold">Full name</label>
                <input name="name" value="{{ old('name', $user->name) }}" class="input mt-1.5" placeholder="Your name">
            </div>
            <div>
                <label class="field-label text-sm font-semibold">Phone number</label>
                <input name="phone" value="{{ old('phone', $user->phone) }}" class="input mt-1.5" placeholder="+91 ...">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="field-label text-sm font-semibold">Currency</label>
                    <select name="currency" class="input mt-1.5">
                        @foreach (['INR','USD','EUR','GBP','AED'] as $c)<option value="{{ $c }}" @selected($user->currency===$c)>{{ $c }}</option>@endforeach
                    </select>
                </div>
                <div>
                    <label class="field-label text-sm font-semibold">Language</label>
                    <select name="locale" class="input mt-1.5">
                        @foreach (['en'=>'English','hi'=>'Hindi'] as $k=>$v)<option value="{{ $k }}" @selected($user->locale===$k)>{{ $v }}</option>@endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Sticky save (app feel on mobile) --}}
        <div class="sticky-action mt-4">
            <button class="btn btn-primary w-full justify-center text-base py-3"><i data-lucide="check" class="w-4 h-4"></i> Save changes</button>
        </div>
    </form>

    {{-- ===== Account & security shortcuts ===== --}}
    <div>
        <p class="list-label">Account &amp; security</p>
        <div class="list-group">
            @foreach ([
                ['dashboard.kyc', 'shield-check', 'KYC Verification', 'Verify identity for withdrawals', 'background:rgba(0,184,169,.12);color:#009688'],
                ['dashboard.notifications', 'bell', 'Notifications', 'Manage alerts & updates', 'background:rgba(255,138,0,.12);color:#c2410c'],
                ['dashboard.support', 'life-buoy', 'Help & Support', 'Get help or raise a ticket', 'background:rgba(15,98,254,.1);color:#0F62FE'],
            ] as $row)
                <a href="{{ route($row[0]) }}" class="list-row press">
                    <span class="list-row-ic" style="{{ $row[4] }}"><i data-lucide="{{ $row[1] }}" class="w-4 h-4"></i></span>
                    <div class="min-w-0 flex-1">
                        <p class="list-row-title">{{ $row[2] }}</p>
                        <p class="list-row-sub">{{ $row[3] }}</p>
                    </div>
                    <i data-lucide="chevron-right" class="list-row-chev w-5 h-5"></i>
                </a>
            @endforeach
        </div>
    </div>

    {{-- ===== Sign out (mobile) ===== --}}
    <form method="POST" action="{{ route('logout') }}" class="lg:hidden">
        @csrf
        <button class="btn w-full justify-center text-danger bg-red-50 border border-red-100 py-3">
            <i data-lucide="log-out" class="w-4 h-4"></i> Sign out
        </button>
    </form>

    <p class="text-center text-xs text-muted pt-2">TripCash · v1.0 · Made with ♥ in India</p>
</div>
@endsection
