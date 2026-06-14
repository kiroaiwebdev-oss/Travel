@extends('layouts.admin')
@section('title', 'Settings')
@section('heading', 'Settings')

@section('content')
<form method="POST" action="{{ route('admin.settings.update') }}" class="max-w-3xl space-y-6">
    @csrf @method('PUT')
    @foreach ($groups as $group => $items)
        <div class="card p-6">
            <h2 class="font-display font-bold capitalize mb-4">{{ $group }}</h2>
            <div class="space-y-4">
                @foreach ($items as $s)
                    <div class="grid sm:grid-cols-3 gap-3 items-center">
                        <label class="text-sm font-semibold">{{ ucwords(str_replace(['.','_'],' ', $s->key)) }}</label>
                        <div class="sm:col-span-2">
                            @if ($s->type === 'bool')
                                <select name="settings[{{ $s->key }}]" class="input">
                                    <option value="1" @selected($s->typedValue())>Enabled</option>
                                    <option value="0" @selected(!$s->typedValue())>Disabled</option>
                                </select>
                            @else
                                <input name="settings[{{ $s->key }}]" value="{{ $s->value }}" class="input">
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
    <button class="btn btn-primary">Save settings</button>
</form>
@endsection
