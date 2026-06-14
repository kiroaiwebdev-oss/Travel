@extends('layouts.dashboard')
@section('title', 'Profile')
@section('heading', 'Profile & Settings')

@section('content')
<div class="grid lg:grid-cols-3 gap-6">
    <div class="card p-6 text-center">
        <div class="mx-auto w-20 h-20 rounded-full bg-primary/10 grid place-items-center text-primary text-2xl font-bold font-display">
            {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>
        <p class="mt-3 font-display font-bold">{{ $user->name }}</p>
        <p class="text-sm text-muted">{{ $user->email }}</p>
        <span class="pill pill-cashback mt-3 inline-flex">{{ $user->status }}</span>
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
