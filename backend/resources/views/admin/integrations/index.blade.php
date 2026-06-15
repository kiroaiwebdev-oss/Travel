@extends('layouts.admin')
@section('title', 'Integrations')
@section('heading', 'Communication & Integrations')

@section('content')
@php
    $badge = fn ($s) => $s['configured']
        ? ($s['enabled'] ? '<span class="pill pill-cashback">Live</span>' : '<span class="pill pill-muted">Configured · off</span>')
        : '<span class="pill pill-deal">Not configured</span>';
@endphp

{{-- Channel status overview --}}
<div class="grid sm:grid-cols-3 gap-4 mb-6">
    @foreach (['email' => ['Email','mail'], 'sms' => ['SMS · Twilio','message-square'], 'whatsapp' => ['WhatsApp Business','message-circle']] as $k => $meta)
        <div class="card p-5">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2"><i data-lucide="{{ $meta[1] }}" class="w-5 h-5 text-pay"></i><span class="font-semibold">{{ $meta[0] }}</span></div>
                {!! $badge($status[$k]) !!}
            </div>
            <p class="text-xs text-muted mt-2">OTP channel: <b class="{{ $otpChannel === $k ? 'text-brand' : '' }}">{{ $otpChannel === $k ? 'active' : '—' }}</b></p>
        </div>
    @endforeach
</div>

<form method="POST" action="{{ route('admin.integrations.update') }}" class="max-w-3xl space-y-6">
    @csrf @method('PUT')

    {{-- OTP + channel toggles --}}
    <div class="card p-6 space-y-4">
        <h2 class="font-display font-bold">Channels & OTP delivery</h2>
        <div class="grid sm:grid-cols-3 gap-3">
            <label class="flex items-center gap-2 text-sm card p-3 cursor-pointer"><input type="checkbox" name="email_enabled" value="1" class="rounded border-slate-300" @checked($status['email']['enabled'])> Email enabled</label>
            <label class="flex items-center gap-2 text-sm card p-3 cursor-pointer"><input type="checkbox" name="sms_enabled" value="1" class="rounded border-slate-300" @checked($status['sms']['enabled'])> SMS enabled</label>
            <label class="flex items-center gap-2 text-sm card p-3 cursor-pointer"><input type="checkbox" name="whatsapp_enabled" value="1" class="rounded border-slate-300" @checked($status['whatsapp']['enabled'])> WhatsApp enabled</label>
        </div>
        <div>
            <label class="text-sm font-semibold">OTP verification channel</label>
            <select name="otp_channel" class="input mt-1 max-w-xs">
                @foreach (['email' => 'Email OTP', 'sms' => 'Mobile SMS OTP', 'whatsapp' => 'WhatsApp OTP'] as $v => $label)
                    <option value="{{ $v }}" @selected($otpChannel === $v)>{{ $label }}</option>
                @endforeach
            </select>
            <p class="text-xs text-muted mt-1">Users verify via the channel you pick here.</p>
        </div>
    </div>

    {{-- Email --}}
    <div class="card p-6 space-y-4">
        <h2 class="font-display font-bold flex items-center gap-2"><i data-lucide="mail" class="w-4 h-4"></i> Email (SMTP)</h2>
        <p class="text-xs text-muted">SMTP host/port/credentials are set in <code>.env</code> (MAIL_*). Here you set the sender identity.</p>
        <div class="grid sm:grid-cols-2 gap-4">
            <div><label class="text-sm font-semibold">From address</label><input name="mail_from_address" value="{{ $get('integrations.mail_from_address') }}" class="input mt-1" placeholder="hello@yourdomain.com"></div>
            <div><label class="text-sm font-semibold">From name</label><input name="mail_from_name" value="{{ $get('integrations.mail_from_name', 'TravelCash') }}" class="input mt-1"></div>
        </div>
    </div>

    {{-- Twilio SMS --}}
    <div class="card p-6 space-y-4">
        <h2 class="font-display font-bold flex items-center gap-2"><i data-lucide="message-square" class="w-4 h-4"></i> SMS · Twilio</h2>
        <div class="grid sm:grid-cols-3 gap-4">
            <div><label class="text-sm font-semibold">Account SID</label><input name="twilio_sid" value="{{ $get('integrations.twilio_sid') }}" class="input mt-1" placeholder="ACxxxx"></div>
            <div><label class="text-sm font-semibold">Auth token</label><input name="twilio_token" type="password" class="input mt-1" placeholder="{{ $get('integrations.twilio_token') ? '•••••• (set)' : '' }}"></div>
            <div><label class="text-sm font-semibold">From number</label><input name="twilio_from" value="{{ $get('integrations.twilio_from') }}" class="input mt-1" placeholder="+1415..."></div>
        </div>
    </div>

    {{-- WhatsApp --}}
    <div class="card p-6 space-y-4">
        <h2 class="font-display font-bold flex items-center gap-2"><i data-lucide="message-circle" class="w-4 h-4"></i> WhatsApp Business (Meta Cloud API)</h2>
        <div class="grid sm:grid-cols-2 gap-4">
            <div><label class="text-sm font-semibold">Access token</label><input name="whatsapp_token" type="password" class="input mt-1" placeholder="{{ $get('integrations.whatsapp_token') ? '•••••• (set)' : '' }}"></div>
            <div><label class="text-sm font-semibold">Phone number ID</label><input name="whatsapp_phone_id" value="{{ $get('integrations.whatsapp_phone_id') }}" class="input mt-1"></div>
        </div>
    </div>

    <button class="btn btn-primary">Save integrations</button>
</form>

{{-- Test a channel --}}
<form method="POST" action="{{ route('admin.integrations.test') }}" class="max-w-3xl card p-6 mt-6 flex flex-wrap items-end gap-3">
    @csrf
    <div><label class="text-sm font-semibold">Test channel</label>
        <select name="channel" class="input mt-1"><option value="email">Email</option><option value="sms">SMS</option><option value="whatsapp">WhatsApp</option></select>
    </div>
    <div class="flex-1 min-w-[200px]"><label class="text-sm font-semibold">Send to (email / phone)</label><input name="to" class="input mt-1" placeholder="test@example.com or +91..."></div>
    <button class="btn btn-dark">Send test</button>
</form>
@endsection
