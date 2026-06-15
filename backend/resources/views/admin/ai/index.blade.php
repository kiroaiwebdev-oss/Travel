@extends('layouts.admin')
@section('title', 'AI Assistant')
@section('heading', 'AI Assistant')

@section('content')
<div class="max-w-3xl space-y-6">

    {{-- Provider status --}}
    <div class="card p-5">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i data-lucide="sparkles" class="w-5 h-5 text-purple-600"></i>
                <h2 class="font-display font-bold">Provider status</h2>
            </div>
            <form method="POST" action="{{ route('admin.ai.test') }}">@csrf<button class="btn btn-ghost text-sm border border-slate-200"><i data-lucide="flask-conical" class="w-4 h-4"></i> Test AI</button></form>
        </div>
        @if ($health)
            <div class="mt-4 flex flex-wrap gap-2">
                @foreach (($health['providers'] ?? []) as $name => $ok)
                    <span class="pill {{ $ok ? 'pill-cashback' : 'pill-muted' }}">
                        <i data-lucide="{{ $ok ? 'check-circle' : 'circle' }}" class="w-3.5 h-3.5"></i> {{ ucfirst($name) }} {{ $ok ? 'configured' : 'no key' }}
                    </span>
                @endforeach
                <span class="pill pill-brand">Priority: {{ implode(' → ', $health['priority'] ?? []) }}</span>
            </div>
            <p class="text-xs text-muted mt-3">If no provider has a key, the assistant still works in a smart demo mode.</p>
        @else
            <p class="text-sm text-warning mt-3 flex items-center gap-2"><i data-lucide="alert-triangle" class="w-4 h-4"></i> AI sidecar not reachable right now (the “ai” container may be starting). Settings still save.</p>
        @endif
    </div>

    <form method="POST" action="{{ route('admin.ai.update') }}" class="space-y-6">
        @csrf @method('PUT')

        {{-- General --}}
        <div class="card p-6 space-y-4">
            <h2 class="font-display font-bold">General</h2>
            <label class="flex items-center justify-between gap-3">
                <span><span class="font-semibold text-sm">Enable AI assistant</span><span class="block text-xs text-muted">Show the assistant on the site and accept questions.</span></span>
                <select name="enabled" class="input w-32">
                    <option value="1" @selected($get('ai.enabled', true))>Enabled</option>
                    <option value="0" @selected(! $get('ai.enabled', true))>Disabled</option>
                </select>
            </label>
            <div><label class="text-sm font-semibold">Assistant name</label><input name="assistant_name" value="{{ $get('ai.assistant_name', 'TripCash AI') }}" class="input mt-1"></div>
            <div><label class="text-sm font-semibold">Welcome message</label><textarea name="welcome_message" rows="2" class="input mt-1">{{ $get('ai.welcome_message') }}</textarea></div>
            <div>
                <label class="text-sm font-semibold">Quick suggestions <span class="text-muted font-normal">(one per line)</span></label>
                <textarea name="suggestions" rows="4" class="input mt-1" placeholder="Best hotel in Goa under ₹5000">{{ $get('ai.suggestions') }}</textarea>
            </div>
        </div>

        {{-- Behaviour --}}
        <div class="card p-6 space-y-4">
            <h2 class="font-display font-bold">Behaviour</h2>
            <div>
                <label class="text-sm font-semibold">System prompt <span class="text-muted font-normal">(leave blank for the default)</span></label>
                <textarea name="system_prompt" rows="5" class="input mt-1" placeholder="You are TripCash's helpful travel assistant…">{{ $get('ai.system_prompt') }}</textarea>
                <p class="text-xs text-muted mt-1">Controls the assistant's tone & rules. The model is always grounded with live cashback offers.</p>
            </div>
            <div>
                <label class="text-sm font-semibold">Provider priority</label>
                <input name="provider_priority" value="{{ $get('ai.provider_priority', 'groq,gemini,openai') }}" class="input mt-1" placeholder="groq,gemini,openai">
                <p class="text-xs text-muted mt-1">Comma-separated fallback order. First configured provider that responds wins.</p>
            </div>
        </div>

        {{-- API keys --}}
        <div class="card p-6 space-y-4">
            <h2 class="font-display font-bold">Provider API keys</h2>
            <p class="text-sm text-muted">Leave a field blank to keep the saved key. Keys are stored securely and never shown back.</p>
            @foreach ([
                ['groq_key', 'Groq API key', 'gsk_…', 'console.groq.com'],
                ['gemini_key', 'Google Gemini API key', 'AIza…', 'aistudio.google.com'],
                ['openai_key', 'OpenAI API key', 'sk-…', 'platform.openai.com'],
            ] as [$field, $label, $ph, $where])
                <div>
                    <label class="text-sm font-semibold">{{ $label }} @if ($get('ai.'.$field))<span class="pill pill-cashback text-[10px] ml-1">saved</span>@endif</label>
                    <input type="password" name="{{ $field }}" autocomplete="new-password" class="input mt-1" placeholder="{{ $get('ai.'.$field) ? '•••••••• (unchanged)' : $ph }}">
                    <p class="text-[11px] text-muted mt-1">Get it from {{ $where }}</p>
                </div>
            @endforeach
        </div>

        <button class="btn btn-primary"><i data-lucide="save" class="w-4 h-4"></i> Save AI settings</button>
    </form>
</div>
@endsection
