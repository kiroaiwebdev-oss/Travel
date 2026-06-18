@extends('layouts.admin')
@section('title', 'Guide & Help')
@section('heading', 'Guide & Help')

@section('content')
@php
$guide = [
    ['title' => 'Getting started', 'icon' => 'rocket', 'items' => [
        ['How TripCash works', 'A user searches → clicks a provider through your affiliate link → the provider sends a signed postback → cashback is credited as “pending” → it matures → the user withdraws. As admin you configure providers, networks and cashback rules, then monitor bookings, cashback and payouts.'],
        ['Your daily flow', 'Check the Dashboard for health, clear pending Withdrawals & KYC, moderate Reviews & Support, and keep an eye on the Cashback ledger for anything that needs confirming or rejecting.'],
        ['The sidebar', 'The left sidebar groups every tool: Overview, Catalog, Finance, Users, Engage and System. Use the toggle in the header to collapse it. Each menu item is explained below.'],
    ]],
    ['title' => 'Overview', 'icon' => 'layout-dashboard', 'items' => [
        ['Dashboard', 'Your morning health-check: total users, active providers, bookings, GMV (booking value), commission earned, pending & paid cashback, pending withdrawals and searches today. The right rail has one-tap quick actions.'],
        ['Analytics', 'Searches per day (last 14 days), the most-searched categories, and revenue by provider. Use it to see what users want and which providers earn you the most.'],
    ]],
    ['title' => 'Catalog — what users can find', 'icon' => 'tag', 'items' => [
        ['Offers & Deals', 'Promotional deals shown on the homepage rails. Fields: title, category, image, cashback label, deep link (your affiliate URL), “Featured” (shows on the landing page), sort order and active toggle. Mark a deal Featured to surface it under Featured hotels/flights/packages.'],
        ['Trending Destinations', 'Controls the homepage “Trending now” circles & the destinations page. Fields: name, tag (e.g. Beaches), image URL, which category it opens, sort order, active. Add/hide/reorder freely.'],
        ['Providers', 'The travel partners users book with (Agoda, Booking, MakeMyTrip…). Tick the categories a provider serves (only ticked categories appear in search), set commission %, priority (lower shows first) and the API key + base URL. With no key it runs in demo mode; add a real key and it switches to live automatically. Toggle active to show/hide.'],
        ['Affiliate Networks', 'Networks group providers (Impact, CJ…). Each has an auto-generated postback secret (used to verify conversions) you can regenerate, and a postback URL you paste into the network’s dashboard so they can report bookings. Toggle active.'],
        ['Cashback rules', 'Decide how much of your commission the user gets. Rules can be global, per-category or per-provider, as a percentage or a flat amount, with a max cap. The most specific rule wins (provider > category > global).'],
    ]],
];
@endphp


@php
$guide = array_merge($guide, [
    ['title' => 'Finance — the money', 'icon' => 'wallet', 'items' => [
        ['Bookings', 'Every booking detected via a provider postback. Monitor status (pending/confirmed/cancelled/completed) and amounts. Bookings are the source of cashback — they’re created server-side from signed postbacks only, never by users.'],
        ['Cashback ledger', 'Every cashback entry and its state: Pending (just detected) → Confirmed (provider validated) → Withdrawable (after the hold window) → or Rejected (booking cancelled). You can manually Confirm, Mature or Reject entries. Rejections safely reverse the pending amount.'],
        ['Withdrawals & Payouts', 'User payout requests. Approve, reject or process them. Withdrawals require completed KYC, can never exceed the user’s withdrawable balance, and are protected against double-spend. Connect a gateway (Razorpay/PayPal) or pay manually and mark as paid.'],
    ]],
    ['title' => 'Users & Access', 'icon' => 'users', 'items' => [
        ['Users', 'Browse and search all users, see wallet balances and activity, change account status (active/suspended), make a manual wallet adjustment (audit-logged), or send a user a message.'],
        ['Staff & Roles', 'Create staff accounts and assign roles. Roles carry granular permissions (providers.manage, withdrawals.approve, cms.manage, etc.) so each team member sees only what they should. This is your access-control center.'],
        ['KYC Review', 'Approve or reject the identity/payout details users submit before their first withdrawal. KYC is mandatory for payouts — this prevents fraud and protects real users.'],
    ]],
    ['title' => 'Engagement', 'icon' => 'megaphone', 'items' => [
        ['Support', 'User support tickets. Open a ticket to read the thread, reply, and change its status (open/resolved).'],
        ['Contact messages', 'Inbox for the public contact form. Read, reply and mark messages handled.'],
        ['Reviews & Suggestions', 'Moderate what users submit on the landing page. Approve to publish, reject to hide, or Feature a great review so it shows first. Filter by status/type (review vs suggestion).'],
        ['Notifications', 'Broadcast announcements / web-push to users (needs VAPID keys configured in Integrations).'],
    ]],
    ['title' => 'System & AI', 'icon' => 'settings', 'items' => [
        ['AI Assistant', 'Full control of the chat assistant: enable/disable, name, welcome message, system prompt, quick-suggestions, provider API keys (Groq/Gemini/OpenAI) + fallback priority, and a live “Test AI” button. Keys are encrypted at rest. No key = smart demo replies.'],
        ['Integrations', 'Email / SMS (Twilio) / WhatsApp channels and which one delivers OTP. Enter credentials (stored encrypted), toggle channels, and send a test message.'],
        ['Settings', 'Site name, tagline, support email; Branding (upload logo & app icon — applies everywhere instantly); Homepage banner (enable + title/subtitle/CTA/link/image); cashback defaults; SEO description.'],
        ['Audit logs', 'An immutable trail of privileged admin actions (who did what, when, from which IP). Your accountability & investigation tool.'],
    ]],
    ['title' => 'Security & fraud (must-read)', 'icon' => 'shield-check', 'items' => [
        ['How fraud is blocked', 'Conversions are only accepted via HMAC-signed postbacks from your networks; click links are signed; wallet balances change only server-side with row-locks + idempotency (no double-credit); referrals block self/same-IP abuse; withdrawals need KYC and can’t exceed balance.'],
        ['Keep these secret', 'Never share or commit your network postback secrets or provider API keys. Set/rotate them only here in the admin panel (they’re encrypted at rest). If a secret leaks, regenerate it on the Affiliate Networks page.'],
        ['Go-live checklist', 'Before launch: APP_DEBUG=false, set a unique APP_KEY (never change it after data exists), change all default DB/service passwords, serve over HTTPS, add real provider/network keys, and NEVER run migrate:fresh in production (it wipes data).'],
    ]],
]);
@endphp


<div class="max-w-4xl space-y-3" x-data="{ open: 0 }">
    {{-- Intro --}}
    <div class="card p-6 relative overflow-hidden" style="background:linear-gradient(120deg,#0B1220,#0d3a52)">
        <div class="relative text-white">
            <div class="flex items-center gap-2">
                <i data-lucide="book-open" class="w-5 h-5 text-teal-300"></i>
                <h2 class="font-display font-extrabold text-lg">Admin guide</h2>
            </div>
            <p class="text-slate-300 text-sm mt-2 max-w-2xl">A complete, plain-language walkthrough of every section, box and button in this admin panel. Tap a topic to expand it. New to TripCash? Start at the top.</p>
        </div>
    </div>

    @foreach ($guide as $i => $g)
        <div class="card overflow-hidden">
            <button @click="open === {{ $i }} ? open = null : open = {{ $i }}" class="w-full flex items-center justify-between p-5 text-left">
                <span class="flex items-center gap-3 font-display font-bold">
                    <span class="w-9 h-9 rounded-xl bg-pay/10 text-pay grid place-items-center shrink-0"><i data-lucide="{{ $g['icon'] }}" class="w-5 h-5"></i></span>
                    {{ $g['title'] }}
                </span>
                <i data-lucide="chevron-down" class="w-5 h-5 text-muted transition shrink-0" :class="open === {{ $i }} && 'rotate-180'"></i>
            </button>
            <div x-show="open === {{ $i }}" x-collapse>
                <div class="px-5 pb-5 space-y-3">
                    @foreach ($g['items'] as [$t, $d])
                        <div class="rounded-xl bg-slate-50 border border-slate-100 p-4">
                            <p class="font-semibold text-sm flex items-center gap-2"><i data-lucide="circle-dot" class="w-3.5 h-3.5 text-brand"></i> {{ $t }}</p>
                            <p class="text-muted text-sm mt-1.5 leading-relaxed">{{ $d }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endforeach

    <p class="text-center text-xs text-muted pt-2">Still stuck? Use the in-app support or contact your platform administrator.</p>
</div>
@endsection
