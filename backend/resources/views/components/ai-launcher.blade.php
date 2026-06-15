{{-- Global floating AI launcher — visible on every page (right side) so users
     are clearly guided to the assistant. Hidden on the assistant page itself and
     when the assistant is disabled by the admin. --}}
@php $aiOn = (bool) \App\Models\Setting::get('ai.enabled', true); @endphp

@if ($aiOn && ! request()->routeIs('assistant'))
    <a href="{{ route('assistant') }}" aria-label="Ask the AI travel assistant"
       class="ai-launcher press fixed right-4 lg:right-6 bottom-24 md:bottom-6 z-[55] flex items-center gap-2 rounded-full pl-1.5 pr-1.5 md:pr-4 py-1.5 text-white"
       style="background:linear-gradient(150deg,#9333ea,#0F62FE); box-shadow:0 18px 40px -12px rgba(124,58,237,.6)">
        <span class="relative grid place-items-center w-11 h-11 rounded-full" style="background:rgba(255,255,255,.18)">
            <span class="absolute inset-0 rounded-full animate-ping" style="background:rgba(255,255,255,.25); animation-duration:2.5s"></span>
            <i data-lucide="sparkles" class="w-5 h-5 relative"></i>
        </span>
        <span class="hidden md:flex flex-col leading-tight pr-1">
            <span class="font-bold text-sm">Ask {{ \App\Models\Setting::get('ai.assistant_name', 'TripCash AI') }}</span>
            <span class="text-[11px] text-white/80">Plan trips · find deals · cashback</span>
        </span>
    </a>
@endif
