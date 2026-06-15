@extends('layouts.admin')
@section('title', 'Staff & Roles')
@section('heading', 'Staff & Roles')

@section('content')
@if ($errors->any())<div class="mb-4 rounded-xl bg-danger/10 text-danger text-sm p-3">{{ $errors->first() }}</div>@endif
<div class="grid lg:grid-cols-3 gap-6">
    <form method="POST" action="{{ route('admin.staff.store') }}" class="card p-6 space-y-4">
        @csrf
        <h2 class="font-display font-bold">Add staff</h2>
        <div><label class="text-sm font-semibold">Name</label><input name="name" class="input mt-1" required></div>
        <div><label class="text-sm font-semibold">Email</label><input type="email" name="email" class="input mt-1" required></div>
        <div><label class="text-sm font-semibold">Password</label><input type="password" name="password" class="input mt-1" required></div>
        <div>
            <label class="text-sm font-semibold">Roles</label>
            <div class="mt-2 space-y-1">
                @foreach ($roles as $r)
                    <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="roles[]" value="{{ $r->id }}" class="rounded border-slate-300"> {{ $r->label }}</label>
                @endforeach
            </div>
        </div>
        <button class="btn btn-primary w-full justify-center">Create staff</button>
    </form>

    <div class="card overflow-hidden lg:col-span-2">
        <h2 class="font-display font-bold p-5 pb-0">Team</h2>
        <table class="w-full text-sm mt-3">
            <thead class="text-left text-muted border-b border-slate-100"><tr><th class="p-4 font-semibold">Member</th><th class="p-4 font-semibold">Roles</th><th class="p-4 font-semibold text-right">Update roles</th></tr></thead>
            <tbody>
                @forelse ($staff as $u)
                    <tr class="border-b border-slate-50 align-top">
                        <td class="p-4"><p class="font-medium">{{ $u->name }}</p><p class="text-xs text-muted">{{ $u->email }}</p></td>
                        <td class="p-4">{{ $u->roles->pluck('label')->join(', ') }}</td>
                        <td class="p-4 text-right">
                            <form method="POST" action="{{ route('admin.staff.roles', $u) }}" class="inline-flex flex-col items-end gap-2">
                                @csrf @method('PUT')
                                <div class="flex flex-wrap gap-2 justify-end">
                                    @foreach ($roles as $r)
                                        <label class="flex items-center gap-1 text-xs"><input type="checkbox" name="roles[]" value="{{ $r->id }}" class="rounded border-slate-300" @checked($u->roles->contains($r->id))> {{ $r->name }}</label>
                                    @endforeach
                                </div>
                                <button class="btn btn-ghost text-xs">Save</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3"><x-empty-state icon="users" text="No staff yet." /></td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4">{{ $staff->links() }}</div>
    </div>
</div>
@endsection
