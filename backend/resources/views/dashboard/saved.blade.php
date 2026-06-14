@extends('layouts.dashboard')
@section('title', 'Saved')
@section('heading', 'Saved & Watchlist')

@section('content')
@php $labels = ['saved_hotel'=>'Saved hotels','saved_flight'=>'Saved flights','saved_search'=>'Saved searches','watchlist'=>'Price watchlist']; @endphp
@if ($items->isEmpty())
    <div class="card p-6"><x-empty-state icon="heart" title="Nothing saved yet" text="Save hotels, flights or searches and they'll show up here." /></div>
@else
    @foreach ($items as $kind => $group)
        <div class="card p-5 mb-5">
            <h2 class="font-display font-bold mb-3">{{ $labels[$kind] ?? ucfirst($kind) }}</h2>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($group as $item)
                    <div class="card p-4 flex items-start justify-between">
                        <div>
                            <p class="font-medium">{{ $item->payload['title'] ?? $item->reference ?? 'Saved item' }}</p>
                            <p class="text-xs text-muted mt-1">{{ ucfirst($item->category) }}@if($item->target_price) · alert at ₹{{ number_format($item->target_price,0) }}@endif</p>
                        </div>
                        <form method="POST" action="{{ route('dashboard.saved.destroy', $item) }}">@csrf @method('DELETE')
                            <button class="text-muted hover:text-danger"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
@endif
@endsection
