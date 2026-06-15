@extends('layouts.dashboard')
@section('title', 'Profile')
@section('heading', 'Profile & Settings')

@section('content')
<div class="grid lg:grid-cols-3 gap-6">
    <div class="card p-6 text-center">
        @if ($user->avatar_url)
            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="mx-auto w-20 h-20 rounded-full object-cover ring-2 ring-brand/30">
        @else
            <div class="mx-auto w-20 h-20 rounded-full bg-primary/10 grid place-items-center text-primary text-2xl font-bold font-display">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
        @endif
        <p class="mt-3 font-display font-bold">{{ $user->name }}</p>
        <p class="text-sm text-muted">{{ $user->email }}</p>
        <span class="pill pill-cashback mt-3 inline-flex">{{ $user->status }}</span>

        <form method="POST" action="{{ route('dashboard.profile.avatar') }}" enctype="multipart/form-data"
              class="mt-5 pt-5 border-t border-slate-100 text-left" x-data="{ name: '' }">
            @csrf
            <label class="text-sm font-semibold">Profile photo</label>
            <input type="file" name="avatar" accept="image/png,image/jpeg,image/webp" required
                   @change="name = $event.target.files[0]?.name"
                   class="mt-2 block w-full text-sm text-muted file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-primary file:text-white file:font-semibold file:cursor-pointer">
            <p class="text-xs text-muted mt-1" x-text="name"></p>
            <p class="text-[11px] text-muted mt-1">JPG / PNG / WEBP, max 2MB.</p>
            <button class="btn btn-primary w-full justify-center mt-3 text-sm"><i data-lucide="upload" class="w-4 h-4"></i> Upload photo</button>
        </form>
    </div>

    <form method="POST" action="{{ route('dashboard.profile.update') }}" class="card p-6 lg:col-span-2 space-y-4">
        @csrf @method('PUT')
        <h2 class="font-display font-bold">Account details</h2>
        <div class="grid sm:grid-cols-2 gap-4">
            <div><label class="text-sm font-semibold">Name</label><input name="name" value="{{ old('name', $user->name) }}" class="input mt-1"></div>
            <div><label class="text-sm font-semibold">Phone</label><input name="phone" value="{{ old('phone', $user->phone) }}" class="input mt-1"></div>
            <div>
                <label class="text-sm font-semibold">Currency</label>
                <select name="currency" class="input mt-1">
                    @foreach (['INR','USD','EUR','GBP','AED'] as $c)<option value="{{ $c }}" @selected($user->currency===$c)>{{ $c }}</option>@endforeach
                </select>
            </div>
            <div>
                <label class="text-sm font-semibold">Language</label>
                <select name="locale" class="input mt-1">
                    @foreach (['en'=>'English','hi'=>'Hindi'] as $k=>$v)<option value="{{ $k }}" @selected($user->locale===$k)>{{ $v }}</option>@endforeach
                </select>
            </div>
        </div>
        <button class="btn btn-primary">Save changes</button>
    </form>
</div>
@endsection
