{{-- Inlined premium design system (always loads, proxy-safe).
     Palette: Trust Blue (primary/buttons) · Travel Teal (secondary/accent) · Green (cashback) · Orange (deals) --}}
<style>
:root{
  --pay:#0F62FE; --pay-700:#0a4fd6;            /* Trust Blue — primary CTA / payment */
  --brand:#00B8A9; --brand-700:#009688;         /* Travel Teal — secondary / accents */
  --cash:#22C55E; --cash-700:#16a34a;           /* Cashback green */
  --deal:#FF8A00;                               /* Deals orange */
  --ink:#1E293B; --muted:#64748B; --bg:#F8FAFC; --card:#FFFFFF;
  --line:rgba(30,41,59,.10); --line-2:rgba(30,41,59,.07);
  --shadow-sm:0 1px 2px rgba(16,24,40,.05);
  --shadow:0 6px 22px -8px rgba(16,24,40,.14), 0 2px 6px -2px rgba(16,24,40,.06);
  --shadow-lg:0 30px 60px -22px rgba(13,42,72,.30);
  --shadow-pay:0 16px 32px -12px rgba(15,98,254,.5);
  --shadow-brand:0 16px 32px -12px rgba(0,184,169,.5);
}
*{ -webkit-font-smoothing:antialiased; text-rendering:optimizeLegibility; }
html{ scroll-behavior:smooth; }
body{ background:var(--bg); color:var(--ink); font-family:'Inter',ui-sans-serif,system-ui,sans-serif; letter-spacing:-0.011em; }
.font-display{ font-family:'Plus Jakarta Sans','Inter',sans-serif; letter-spacing:-0.03em; }

/* color helpers */
.bg-bg{background:var(--bg)} .bg-card{background:var(--card)} .text-ink{color:var(--ink)}
.text-muted{color:var(--muted)} .text-brand{color:var(--brand-700)} .bg-brand{background:var(--brand)}
.text-pay{color:var(--pay)} .bg-pay{background:var(--pay)} .text-deal{color:var(--deal)}
.text-accent{color:var(--brand)} .bg-secondary{background:#0B1220}
.text-success{color:var(--cash-700)} .text-warning{color:#d97706} .text-danger{color:#dc2626}

/* ===== Premium hero backdrop ===== */
.hero-aurora{ position:relative; overflow:hidden; background:
  radial-gradient(60% 50% at 8% -10%, rgba(0,184,169,.20), transparent 55%),
  radial-gradient(55% 50% at 100% -10%, rgba(15,98,254,.16), transparent 55%),
  radial-gradient(40% 40% at 60% 0%, rgba(34,197,94,.10), transparent 60%),
  linear-gradient(180deg,#ffffff 0%, var(--bg) 70%); }
.hero-aurora::before{ content:""; position:absolute; inset:0;
  background-image:linear-gradient(rgba(30,41,59,.04) 1px,transparent 1px),linear-gradient(90deg,rgba(30,41,59,.04) 1px,transparent 1px);
  background-size:34px 34px; -webkit-mask-image:radial-gradient(70% 55% at 50% 0%,#000,transparent 75%); mask-image:radial-gradient(70% 55% at 50% 0%,#000,transparent 75%); pointer-events:none; }
.text-gradient{ background:linear-gradient(100deg,var(--brand) 0%, var(--pay) 110%); -webkit-background-clip:text; background-clip:text; color:transparent; }

/* ===== Glass ===== */
.glass{ background:rgba(255,255,255,.72); backdrop-filter:saturate(180%) blur(16px); -webkit-backdrop-filter:saturate(180%) blur(16px); border:1px solid rgba(255,255,255,.6); }

/* ===== Buttons ===== */
.btn{ display:inline-flex; align-items:center; justify-content:center; gap:.5rem; font-weight:600; font-size:.9rem; line-height:1; border-radius:.85rem; padding:.72rem 1.2rem; transition:transform .15s ease, box-shadow .22s ease, background .2s, opacity .2s; cursor:pointer; white-space:nowrap; border:1px solid transparent; }
.btn:active{ transform:translateY(0) scale(.985); }
.btn-primary,.btn-pay{ background:linear-gradient(180deg,#3b82f6,#0F62FE); color:#fff; box-shadow:var(--shadow-pay); }
.btn-primary:hover,.btn-pay:hover{ transform:translateY(-1px); box-shadow:0 20px 38px -12px rgba(15,98,254,.62); }
.btn-brand{ background:linear-gradient(180deg,#14c8b8,#00B8A9); color:#fff; box-shadow:var(--shadow-brand); }
.btn-brand:hover{ transform:translateY(-1px); box-shadow:0 20px 38px -12px rgba(0,184,169,.6); }
.btn-dark{ background:linear-gradient(180deg,#28344a,#1E293B); color:#fff; box-shadow:var(--shadow); }
.btn-dark:hover{ transform:translateY(-1px); opacity:.95; }
.btn-ghost{ background:transparent; color:var(--ink); }
.btn-ghost:hover{ background:rgba(30,41,59,.05); }
.btn-white{ background:#fff; color:var(--ink); box-shadow:var(--shadow); }
.btn-white:hover{ transform:translateY(-1px); }

/* ===== Cards ===== */
.card{ background:var(--card); border:1px solid var(--line-2); border-radius:1.2rem; box-shadow:var(--shadow); }
.card-hover{ transition:transform .24s cubic-bezier(.2,.7,.3,1), box-shadow .24s, border-color .24s; }
.card-hover:hover{ transform:translateY(-5px); box-shadow:var(--shadow-lg); border-color:rgba(0,184,169,.28); }

/* ===== Pills / badges ===== */
.pill{ display:inline-flex; align-items:center; gap:.35rem; font-size:.72rem; font-weight:700; padding:.3rem .6rem; border-radius:999px; line-height:1; }
.pill-cashback{ background:linear-gradient(180deg,rgba(34,197,94,.18),rgba(34,197,94,.1)); color:#15803d; border:1px solid rgba(34,197,94,.25); }
.pill-deal{ background:linear-gradient(180deg,rgba(255,138,0,.18),rgba(255,138,0,.1)); color:#c2410c; border:1px solid rgba(255,138,0,.28); }
.pill-brand{ background:rgba(0,184,169,.1); color:var(--brand-700); border:1px solid rgba(0,184,169,.22); }
.pill-muted{ background:rgba(100,116,139,.1); color:#475569; }

/* ===== Inputs ===== */
.input{ width:100%; border:1px solid var(--line); border-radius:.8rem; padding:.7rem .9rem; font-size:.92rem; background:#fff; color:var(--ink); transition:border-color .15s, box-shadow .15s; }
.input::placeholder{ color:#94a3b8; }
.input:focus{ outline:none; border-color:var(--brand); box-shadow:0 0 0 4px rgba(0,184,169,.18); }
select.input{ appearance:none; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='none' stroke='%2364748b' stroke-width='2'%3E%3Cpath d='M4 6l4 4 4-4'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right .7rem center; padding-right:2rem; }

/* ===== Sidebar nav ===== */
.nav-link{ display:flex; align-items:center; gap:.7rem; padding:.62rem .82rem; border-radius:.7rem; color:var(--muted); font-weight:500; font-size:.92rem; transition:.15s; }
.nav-link:hover{ background:rgba(15,98,254,.07); color:var(--ink); }
.nav-link.active{ background:linear-gradient(180deg,rgba(15,98,254,.12),rgba(15,98,254,.07)); color:var(--pay); font-weight:600; }

/* ===== Bottom nav (PWA / mobile) ===== */
.bnav{ position:fixed; bottom:0; left:0; right:0; z-index:60; display:flex; padding:.4rem .5rem calc(.4rem + env(safe-area-inset-bottom)); background:rgba(255,255,255,.9); backdrop-filter:blur(14px); -webkit-backdrop-filter:blur(14px); border-top:1px solid var(--line-2); }
.bnav a{ flex:1; display:flex; flex-direction:column; align-items:center; gap:.15rem; padding:.4rem; border-radius:.7rem; color:var(--muted); font-size:.66rem; font-weight:600; transition:.15s; }
.bnav a.active{ color:var(--pay); }
.bnav a.active .bnav-ic{ background:rgba(15,98,254,.12); }
.bnav-ic{ display:grid; place-items:center; width:2.1rem; height:1.7rem; border-radius:.6rem; transition:.15s; }

/* ===== Effects ===== */
.counter{ font-variant-numeric:tabular-nums; }
.ring-grid{ background-image:radial-gradient(rgba(30,41,59,.05) 1px,transparent 1px); background-size:22px 22px; }
.skeleton{ position:relative; overflow:hidden; background:#eef2f7; }
.skeleton::after{ content:""; position:absolute; inset:0; transform:translateX(-100%); background:linear-gradient(90deg,transparent,rgba(255,255,255,.7),transparent); animation:shimmer 1.4s infinite; }
@keyframes shimmer{ 100%{ transform:translateX(100%);} }
@keyframes fadeup{ from{ opacity:0; transform:translateY(12px);} to{ opacity:1; transform:none;} }
@keyframes fadein{ from{ opacity:0;} to{ opacity:1;} }
@keyframes slideRight{ from{ opacity:0; transform:translateX(-16px);} to{ opacity:1; transform:none;} }
@keyframes slideLeft{ from{ opacity:0; transform:translateX(16px);} to{ opacity:1; transform:none;} }
@keyframes scaleIn{ from{ opacity:0; transform:scale(.92);} to{ opacity:1; transform:none;} }
@keyframes float{ 0%,100%{ transform:translateY(0px);} 50%{ transform:translateY(-8px);} }
@keyframes pulse-soft{ 0%,100%{ opacity:1;} 50%{ opacity:.7;} }
@keyframes gradient-shift{ 0%{ background-position:0% 50%;} 50%{ background-position:100% 50%;} 100%{ background-position:0% 50%;} }

.fade-up{ animation:fadeup .6s cubic-bezier(.2,.7,.3,1) both; }
.fade-up-2{ animation:fadeup .6s .08s cubic-bezier(.2,.7,.3,1) both; }
.fade-up-3{ animation:fadeup .6s .16s cubic-bezier(.2,.7,.3,1) both; }
.fade-up-4{ animation:fadeup .6s .24s cubic-bezier(.2,.7,.3,1) both; }
.fade-in{ animation:fadein .5s ease both; }
.slide-right{ animation:slideRight .6s cubic-bezier(.2,.7,.3,1) both; }
.slide-left{ animation:slideLeft .6s cubic-bezier(.2,.7,.3,1) both; }
.scale-in{ animation:scaleIn .5s cubic-bezier(.2,.7,.3,1) both; }
.float-anim{ animation:float 4s ease-in-out infinite; }
.pulse-soft{ animation:pulse-soft 2.5s ease-in-out infinite; }
.gradient-shift{ background-size:200% 200%; animation:gradient-shift 5s ease infinite; }

/* Premium hover effects */
.hover-lift{ transition:transform .3s cubic-bezier(.2,.7,.3,1), box-shadow .3s; }
.hover-lift:hover{ transform:translateY(-6px); box-shadow:var(--shadow-lg); }
.hover-glow:hover{ box-shadow:0 0 20px rgba(0,184,169,.2), 0 0 40px rgba(0,184,169,.1); }
.hover-scale{ transition:transform .3s cubic-bezier(.2,.7,.3,1); }
.hover-scale:hover{ transform:scale(1.03); }

/* Gradient text animation */
.text-gradient-animated{
  background:linear-gradient(100deg,var(--brand) 0%, var(--pay) 50%, var(--brand) 100%);
  background-size:200% 100%;
  -webkit-background-clip:text; background-clip:text; color:transparent;
  animation:gradient-shift 4s ease infinite;
}

/* Section dividers */
.section-divider{ position:relative; }
.section-divider::before{ content:""; position:absolute; top:0; left:50%; transform:translateX(-50%); width:60px; height:4px; border-radius:999px; background:linear-gradient(90deg,var(--brand),var(--pay)); }

/* Scroll reveal (works with x-intersect) */
.reveal{ opacity:0; transform:translateY(20px); transition:opacity .6s cubic-bezier(.2,.7,.3,1), transform .6s cubic-bezier(.2,.7,.3,1); }
.reveal.visible{ opacity:1; transform:none; }

[x-cloak]{ display:none !important; }
.line-clamp-2{ display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
.pb-safe{ padding-bottom:calc(5rem + env(safe-area-inset-bottom)); }
@media (min-width:768px){ .pb-safe{ padding-bottom:0; } }

/* Premium scrollbar */
::-webkit-scrollbar{ width:6px; height:6px; }
::-webkit-scrollbar-track{ background:transparent; }
::-webkit-scrollbar-thumb{ background:rgba(100,116,139,.3); border-radius:999px; }
::-webkit-scrollbar-thumb:hover{ background:rgba(100,116,139,.5); }

/* Selection */
::selection{ background:rgba(0,184,169,.2); }

/* Focus styles */
:focus-visible{ outline:2px solid var(--brand); outline-offset:2px; border-radius:4px; }
</style>
