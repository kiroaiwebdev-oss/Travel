@extends('layouts.admin')
@section('title', 'Affiliate Networks')
@section('heading', 'Affiliate Networks')

@section('content')
<div class="grid lg:grid-cols-3 gap-6">
    <form method="POST" action="{{ route('admin.networks.store') }}" class="card p-6 space-y-4">
        @csrf
        <h2 class="font-display font-bold">Add network</h2>
        <p class="text-sm text-muted">e.g. Impact, CJ, Admitad, Cuelinks. A postback secret is auto-generated.</p>
        <div><label class="text-sm font-semibold">Name</label><input name="name" class="input mt-1" required></div>
        <button class="btn btn-primary w-full justify-center">Add network</button>
    </form>

    <div class="card overflow-hidden lg:col-span-2">
        <h2 class="font-display font-bold p-5 pb-0">Networks</h2>
        <table class="w-full text-sm mt-3">
            <thead class="text-left text-muted border-b border-slate-100"><tr><th class="p-4 font-semibold">Network</th><th class="p-4 font-semibold">Providers</th><th class="p-4 font-semibold">Postback URL</th><th class="p-4 font-semibold">Status</th><th class="p-4 font-semibold text-right">Actions</th></tr></thead>
            <tbody>
                @forelse ($networks as $n)
                    <tr class="border-b border-slate-50">
                        <td class="p-4 font-medium">{{ $n->name }}<br><span class="text-xs text-muted font-mono">{{ $n->slug }}</span></td>
                        <td class="p-4">{{ $n->providers_count }}</td>
                        <td class="p-4"><code class="text-xs">/api/v1/postback/{{ $n->slug }}</code></td>
                        <td class="p-4">
                            <form method="POST" action="{{ route('admin.networks.toggle', $n) }}">@csrf @method('PUT')<button class="pill {{ $n->is_active ? 'pill-cashback' : 'pill-muted' }}">{{ $n->is_active ? 'Active' : 'Off' }}</button></form>
                        </td>
                        <td class="p-4 text-right">
                            <form method="POST" action="{{ route('admin.networks.secret', $n) }}" class="inline" onsubmit="return confirm('Regenerate postback secret? You must update it in the network dashboard.')">@csrf @method('PUT')<button class="btn btn-ghost text-xs">New secret</button></form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5"><x-empty-state icon="network" text="No networks yet." /></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
