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
/* ===== Date inputs — match text inputs for a clean, premium, consistent look ===== */
.date-field input[type="date"]{ color:var(--ink); -webkit-appearance:none; appearance:none; }
.date-field input[type="date"]::-webkit-calendar-picker-indicator{ position:absolute; inset:0; width:100%; height:100%; margin:0; padding:0; opacity:0; cursor:pointer; }
.date-field input[type="date"]::-webkit-datetime-edit{ line-height:1.2; }
.date-field input[type="date"]::-webkit-datetime-edit-text,
.date-field input[type="date"]::-webkit-datetime-edit-month-field,
.date-field input[type="date"]::-webkit-datetime-edit-day-field,
.date-field input[type="date"]::-webkit-datetime-edit-year-field{ color:#94a3b8; }
.date-field input[type="date"]:focus::-webkit-datetime-edit-text,
.date-field input[type="date"]:focus::-webkit-datetime-edit-month-field,
.date-field input[type="date"]:focus::-webkit-datetime-edit-day-field,
.date-field input[type="date"]:focus::-webkit-datetime-edit-year-field{ color:var(--ink); }
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

/* ============================================================
   ===== NATIVE-APP-FEEL LAYER (mobile-first) =================
   On phones the site behaves like an installed app; on >=768px
   it falls back to the regular website layout.
   ============================================================ */

/* prevent text auto-zoom & give native tap feedback on mobile */
@media (max-width:767px){
  html{ -webkit-text-size-adjust:100%; }
  body{ overscroll-behavior-y:none; }
  /* native tap highlight removal + active press feel */
  a,button,[role="button"],.tap{ -webkit-tap-highlight-color:transparent; }
  .tap:active,a:active,button:active{ }
}
.press{ transition:transform .12s ease; }
.press:active{ transform:scale(.96); }

/* ===== App top header (mobile only) ===== */
.app-header{ position:sticky; top:0; z-index:50; padding-top:env(safe-area-inset-top);
  background:rgba(255,255,255,.92); backdrop-filter:saturate(180%) blur(16px); -webkit-backdrop-filter:saturate(180%) blur(16px);
  border-bottom:1px solid var(--line-2); }
.app-header-gradient{ background:linear-gradient(135deg,#0d9488 0%,#0f766e 45%,#0F62FE 130%); border-bottom:none; }

/* ===== Horizontal snap rails (story / card carousels) ===== */
.h-scroll{ display:flex; gap:.75rem; overflow-x:auto; scroll-snap-type:x mandatory; -webkit-overflow-scrolling:touch; scroll-padding-left:1rem; }
.h-scroll > *{ scroll-snap-align:start; flex:0 0 auto; }
.no-scrollbar{ -ms-overflow-style:none; scrollbar-width:none; }
.no-scrollbar::-webkit-scrollbar{ display:none; width:0; height:0; }

/* ===== Story circles (Instagram-style) ===== */
.story{ width:4.6rem; }
.story-ring{ width:4.6rem; height:4.6rem; padding:2.5px; border-radius:999px; background:linear-gradient(135deg,#14b8a6,#0F62FE,#FF8A00); }
.story-ring-inner{ width:100%; height:100%; padding:2px; border-radius:999px; background:#fff; }
.story img{ width:100%; height:100%; border-radius:999px; object-fit:cover; object-position:center; display:block; }

/* ===== Segmented control (app tabs) ===== */
.segmented{ display:inline-flex; padding:.25rem; gap:.15rem; background:rgba(30,41,59,.06); border-radius:999px; }
.segmented button{ padding:.45rem .9rem; border-radius:999px; font-size:.82rem; font-weight:600; color:var(--muted); transition:.2s; white-space:nowrap; }
.segmented button.on{ background:#fff; color:var(--ink); box-shadow:var(--shadow-sm); }

/* ===== App list rows ===== */
.app-row{ display:flex; align-items:center; gap:.85rem; padding:.85rem 1rem; background:#fff; }
.app-row + .app-row{ border-top:1px solid var(--line-2); }
.app-row-ic{ width:2.6rem; height:2.6rem; border-radius:.85rem; display:grid; place-items:center; flex:0 0 auto; }

/* ===== Bottom nav upgrade: center FAB + labels ===== */
.bnav{ box-shadow:0 -4px 24px -10px rgba(13,42,72,.18); }
.bnav-fab{ position:relative; }
.bnav-fab a{ position:absolute; left:50%; top:-1.6rem; transform:translateX(-50%); width:3.6rem; height:3.6rem; border-radius:999px;
  display:grid; place-items:center; color:#fff; box-shadow:0 12px 24px -8px rgba(15,98,254,.6);
  background:linear-gradient(160deg,#14b8a6,#0F62FE); border:4px solid #fff; }
.bnav-fab a.active .bnav-ic,.bnav-fab a .bnav-ic{ background:transparent; }

/* ===== Bottom sheet ===== */
.sheet-backdrop{ position:fixed; inset:0; z-index:80; background:rgba(11,18,32,.5); backdrop-filter:blur(2px); }
.sheet{ position:fixed; left:0; right:0; bottom:0; z-index:81; background:#fff; border-radius:1.5rem 1.5rem 0 0;
  padding:.5rem 1rem calc(1.25rem + env(safe-area-inset-bottom)); box-shadow:0 -24px 60px -20px rgba(13,42,72,.4); max-height:88vh; overflow-y:auto; }
.sheet-handle{ width:2.6rem; height:.32rem; border-radius:999px; background:rgba(30,41,59,.2); margin:.5rem auto 1rem; }

/* sheet enter/leave */
@keyframes sheetUp{ from{ transform:translateY(100%);} to{ transform:translateY(0);} }
.sheet{ animation:sheetUp .32s cubic-bezier(.2,.8,.2,1) both; }

/* ===== App promo / balance card ===== */
.app-balance{ background:linear-gradient(135deg,#0B1220 0%,#0f2e2b 55%,#0d3a52 120%); color:#fff; border-radius:1.25rem; position:relative; overflow:hidden; }
.app-balance::after{ content:""; position:absolute; right:-30px; top:-30px; width:140px; height:140px; border-radius:999px; background:rgba(13,148,136,.4); filter:blur(28px); }

/* ===== Quick-action grid (app shortcuts) ===== */
.qa{ display:flex; flex-direction:column; align-items:center; gap:.4rem; }
.qa-ic{ width:3.2rem; height:3.2rem; border-radius:1.1rem; display:grid; place-items:center; }
.qa span{ font-size:.7rem; font-weight:600; color:var(--ink); }

/* ===== Section title (app) ===== */
.app-sec-title{ display:flex; align-items:center; justify-content:space-between; padding:0 1rem; }
.app-sec-title h3{ font-weight:800; font-size:1.02rem; }
.app-sec-title a{ font-size:.78rem; font-weight:700; color:var(--pay); }

/* tighter edge spacing on phones */
@media (max-width:767px){
  .app-edge{ padding-left:1rem; padding-right:1rem; }
}

/* ===== App top bar (dashboard) ===== */
.app-topbar{ position:sticky; top:0; z-index:40; padding-top:env(safe-area-inset-top);
  background:rgba(255,255,255,.92); backdrop-filter:saturate(180%) blur(16px); -webkit-backdrop-filter:saturate(180%) blur(16px);
  border-bottom:1px solid var(--line-2); }
.app-topbar-row{ height:3.5rem; display:flex; align-items:center; gap:.5rem; padding:0 .5rem; }
.app-iconbtn{ width:2.4rem; height:2.4rem; border-radius:999px; display:grid; place-items:center; color:var(--ink); transition:.15s; }
.app-iconbtn:active{ background:rgba(30,41,59,.08); transform:scale(.94); }

/* ===== Grouped settings / list cards (iOS-style) ===== */
.list-group{ background:#fff; border:1px solid var(--line-2); border-radius:1.1rem; overflow:hidden; box-shadow:var(--shadow-sm); }
.list-row{ display:flex; align-items:center; gap:.85rem; padding:.9rem 1rem; background:#fff; transition:.12s; }
.list-row:active{ background:rgba(30,41,59,.04); }
.list-row + .list-row{ border-top:1px solid var(--line-2); }
.list-row-ic{ width:2.3rem; height:2.3rem; border-radius:.7rem; display:grid; place-items:center; flex:0 0 auto; }
.list-row-title{ font-weight:600; font-size:.92rem; }
.list-row-sub{ font-size:.76rem; color:var(--muted); }
.list-row-chev{ color:#cbd5e1; margin-left:auto; flex:0 0 auto; }
.list-label{ font-size:.72rem; font-weight:700; letter-spacing:.04em; text-transform:uppercase; color:var(--muted); padding:0 1rem .5rem; }

/* ===== App field (bigger touch input) ===== */
@media (max-width:767px){
  .input{ padding:.85rem .95rem; font-size:1rem; border-radius:.85rem; }
  .field-label{ font-size:.8rem; }
}

/* ===== Sticky bottom action bar (forms) ===== */
.sticky-action{ position:sticky; bottom:0; z-index:30; margin:0 -1rem; padding:.75rem 1rem calc(.75rem + env(safe-area-inset-bottom));
  background:rgba(255,255,255,.92); backdrop-filter:blur(12px); -webkit-backdrop-filter:blur(12px); border-top:1px solid var(--line-2); }
@media (min-width:768px){ .sticky-action{ position:static; margin:0; padding:0; background:none; border:none; backdrop-filter:none; } }

/* ===== Toggle switch ===== */
.switch{ position:relative; width:2.7rem; height:1.6rem; border-radius:999px; background:#cbd5e1; transition:.2s; flex:0 0 auto; cursor:pointer; }
.switch::after{ content:""; position:absolute; top:.18rem; left:.18rem; width:1.24rem; height:1.24rem; border-radius:999px; background:#fff; box-shadow:var(--shadow-sm); transition:.2s; }
.switch.on{ background:var(--brand); }
.switch.on::after{ transform:translateX(1.1rem); }

/* ===== Slide-in drawer (mobile full menu) ===== */
.drawer{ position:fixed; inset-y:0; left:0; top:0; bottom:0; z-index:90; width:78%; max-width:20rem; background:#fff;
  padding:calc(env(safe-area-inset-top) + .5rem) .75rem 1rem; box-shadow:0 0 60px rgba(13,42,72,.35); overflow-y:auto; }

/* ===== Install banner ===== */
.install-banner{ position:fixed; left:.75rem; right:.75rem; bottom:calc(4.8rem + env(safe-area-inset-bottom)); z-index:70;
  background:#fff; border:1px solid var(--line-2); border-radius:1.1rem; box-shadow:var(--shadow-lg); padding:.85rem 1rem; }
@media (min-width:768px){ .install-banner{ left:auto; right:1.25rem; bottom:1.25rem; max-width:22rem; } }

/* ===== PWA standalone (installed app) refinements ===== */
@media (display-mode: standalone), (display-mode: minimal-ui){
  /* hide browser-only chrome hints when running as an installed app */
  .only-browser{ display:none !important; }
  /* prevent accidental text selection on UI chrome for a native feel */
  .app-header, .app-topbar, .bnav, .list-row-title, .qa span{ -webkit-user-select:none; user-select:none; }
  body{ overscroll-behavior:none; }
}
html.standalone .hide-in-app{ display:none !important; }

/* ============================================================
   ===== NEOMORPHISM + SKEUOMORPHISM PREMIUM LAYER ============
   Adds tactile depth (soft neumorphic dual shadows) + skeuomorphic
   gloss / highlights across the shared component system, so the
   entire frontend (marketing, dashboard, admin) gets a premium,
   physical "soft UI" feel at once. Loaded last so it refines the
   base components via the cascade. Text contrast is preserved.
   ============================================================ */
:root{
  --neo-bg:#e8edf4;                 /* neomorphic base surface (page) */
  --neo-surface:#eef2f8;            /* raised element surface */
  --neo-light:#ffffff;              /* top-left highlight */
  --neo-dark:#c4cde0;               /* bottom-right shadow */
  /* raised (extruded) */
  --neo-raised-sm:-3px -3px 7px var(--neo-light), 3px 3px 8px var(--neo-dark);
  --neo-raised:-6px -6px 14px var(--neo-light), 7px 7px 18px var(--neo-dark);
  --neo-raised-lg:-10px -10px 26px var(--neo-light), 12px 12px 30px var(--neo-dark);
  /* inset (pressed / well) */
  --neo-inset:inset 3px 3px 6px var(--neo-dark), inset -3px -3px 6px var(--neo-light);
  --neo-inset-deep:inset 5px 5px 11px var(--neo-dark), inset -4px -4px 9px var(--neo-light);
  /* skeuomorphic gloss overlays */
  --gloss-soft:linear-gradient(180deg, rgba(255,255,255,.6), rgba(255,255,255,0) 48%);
  --gloss-strong:linear-gradient(180deg, rgba(255,255,255,.45) 0%, rgba(255,255,255,.12) 42%, rgba(0,0,0,.06) 100%);
  /* refresh palette shadows to feel softer over the neo base */
  --shadow-sm:0 1px 2px rgba(20,30,55,.06);
}

body{ background:var(--neo-bg) !important; }
.bg-bg{ background:var(--neo-bg) !important; }
.hero-aurora{ background:
  radial-gradient(60% 50% at 8% -10%, rgba(0,184,169,.20), transparent 55%),
  radial-gradient(55% 50% at 100% -10%, rgba(15,98,254,.16), transparent 55%),
  radial-gradient(40% 40% at 60% 0%, rgba(34,197,94,.10), transparent 60%),
  linear-gradient(180deg,#f4f7fc 0%, var(--neo-bg) 70%); }

/* ===== Cards → neomorphic raised + skeuomorphic gloss ===== */
.card{
  background:linear-gradient(145deg, #ffffff, var(--neo-surface));
  border:1px solid rgba(255,255,255,.7);
  border-radius:1.35rem;
  box-shadow:var(--neo-raised);
  position:relative;
}
.card::before{
  content:""; position:absolute; inset:0; border-radius:inherit; pointer-events:none;
  background:var(--gloss-soft); opacity:.5;
}
.card > *{ position:relative; }
.card-hover{ transition:transform .26s cubic-bezier(.2,.7,.3,1), box-shadow .26s; }
.card-hover:hover{ transform:translateY(-5px); box-shadow:var(--neo-raised-lg); border-color:rgba(0,184,169,.3); }
.list-group{ background:linear-gradient(145deg,#ffffff,var(--neo-surface)); border:1px solid rgba(255,255,255,.7); box-shadow:var(--neo-raised); border-radius:1.25rem; }

/* ===== Glass → add inner highlight for tactile depth ===== */
.glass{ box-shadow:var(--neo-raised-sm), inset 0 1px 0 rgba(255,255,255,.7); }

/* ===== Buttons → skeuomorphic gloss + neumorphic press ===== */
.btn{ position:relative; overflow:hidden; border:none; isolation:isolate; }
.btn::after{ content:""; position:absolute; inset:0; border-radius:inherit; pointer-events:none; background:var(--gloss-strong); opacity:.85; mix-blend-mode:soft-light; }
.btn:active{ transform:translateY(1px) scale(.99); }
.btn-primary,.btn-pay{ box-shadow:0 10px 22px -10px rgba(15,98,254,.6), inset 0 1px 0 rgba(255,255,255,.45), inset 0 -2px 4px rgba(7,40,110,.4); }
.btn-primary:hover,.btn-pay:hover{ box-shadow:0 16px 30px -12px rgba(15,98,254,.66), inset 0 1px 0 rgba(255,255,255,.5), inset 0 -2px 4px rgba(7,40,110,.4); }
.btn-primary:active,.btn-pay:active{ box-shadow:0 3px 8px -4px rgba(15,98,254,.5), inset 0 2px 6px rgba(7,40,110,.5); }
.btn-brand{ box-shadow:0 10px 22px -10px rgba(0,184,169,.55), inset 0 1px 0 rgba(255,255,255,.45), inset 0 -2px 4px rgba(0,90,82,.4); }
.btn-brand:active{ box-shadow:0 3px 8px -4px rgba(0,184,169,.5), inset 0 2px 6px rgba(0,90,82,.5); }
.btn-dark{ box-shadow:0 10px 22px -10px rgba(13,24,44,.6), inset 0 1px 0 rgba(255,255,255,.14), inset 0 -2px 4px rgba(0,0,0,.4); }
.btn-white{ background:linear-gradient(180deg,#ffffff,var(--neo-surface)); box-shadow:var(--neo-raised-sm); }
.btn-white:hover{ box-shadow:var(--neo-raised); }
.btn-white:active{ box-shadow:var(--neo-inset); }
.btn-ghost{ box-shadow:none; }
.btn-ghost::after{ opacity:0; }
.btn-ghost:active{ box-shadow:var(--neo-inset); background:rgba(30,41,59,.04); }

/* ===== Inputs → inset neumorphic wells ===== */
.input{
  background:var(--neo-bg);
  border:1px solid rgba(255,255,255,.6);
  box-shadow:var(--neo-inset);
  transition:box-shadow .18s, border-color .18s;
}
.input::placeholder{ color:#8b97ab; }
.input:focus{ border-color:rgba(0,184,169,.5); box-shadow:var(--neo-inset-deep), 0 0 0 3px rgba(0,184,169,.18); }
select.input{ background-color:var(--neo-bg); }

/* ===== Pills → soft raised chips ===== */
.pill{ box-shadow:var(--neo-raised-sm); }
.pill-muted{ background:linear-gradient(145deg,#fff,var(--neo-surface)); color:#475569; }

/* ===== Sidebar nav → tactile active state ===== */
.nav-link:hover{ box-shadow:var(--neo-inset); background:transparent; }
.nav-link.active{ box-shadow:var(--neo-raised-sm); background:linear-gradient(145deg,#fff,#eaf0fb); }

/* ===== Segmented control → inset track, raised selected ===== */
.segmented{ background:var(--neo-bg); box-shadow:var(--neo-inset); }
.segmented button.on{ background:linear-gradient(145deg,#fff,var(--neo-surface)); box-shadow:var(--neo-raised-sm); }

/* ===== Toggle switch → inset track + glossy raised knob ===== */
.switch{ background:var(--neo-bg); box-shadow:var(--neo-inset); }
.switch::after{ background:linear-gradient(145deg,#fff,#e9eef6); box-shadow:var(--neo-raised-sm); }
.switch.on{ background:linear-gradient(180deg,#16c8b8,var(--brand)); box-shadow:var(--neo-inset), inset 0 1px 2px rgba(0,90,82,.3); }

/* ===== Bottom nav → glossy raised bar + tactile icons ===== */
.bnav{ background:linear-gradient(180deg, rgba(255,255,255,.96), rgba(244,247,252,.94)); box-shadow:0 -6px 26px -10px rgba(13,42,72,.22), inset 0 1px 0 rgba(255,255,255,.8); }
.bnav a.active .bnav-ic{ box-shadow:var(--neo-inset); background:var(--neo-bg); }
.bnav-fab a{ box-shadow:0 12px 24px -8px rgba(15,98,254,.6), inset 0 2px 3px rgba(255,255,255,.45), inset 0 -3px 5px rgba(7,40,110,.4); }
.bnav-fab a:active{ transform:translateX(-50%) scale(.94); }

/* ===== Quick-action / list / app icons → neumorphic tiles ===== */
.qa-ic{ box-shadow:var(--neo-raised-sm); position:relative; }
.qa-ic::after{ content:""; position:absolute; inset:0; border-radius:inherit; background:var(--gloss-soft); opacity:.5; pointer-events:none; }
.list-row-ic,.app-row-ic{ box-shadow:var(--neo-raised-sm); }
.app-iconbtn{ background:linear-gradient(145deg,#fff,var(--neo-surface)); box-shadow:var(--neo-raised-sm); }
.app-iconbtn:active{ box-shadow:var(--neo-inset); transform:scale(.94); }

/* ===== App headers / topbars → subtle gloss edge ===== */
.app-header,.app-topbar{ box-shadow:inset 0 1px 0 rgba(255,255,255,.75), 0 4px 18px -12px rgba(13,42,72,.25); }

/* ===== Balance card → glossy skeuomorphic sheen ===== */
.app-balance{ box-shadow:0 18px 40px -18px rgba(7,40,80,.6), inset 0 1px 0 rgba(255,255,255,.18); }
.app-balance::before{ content:""; position:absolute; left:0; right:0; top:0; height:42%; border-radius:1.25rem 1.25rem 0 0; background:linear-gradient(180deg,rgba(255,255,255,.16),transparent); pointer-events:none; }

/* ===== Sheet / drawer → soft top highlight ===== */
.sheet{ box-shadow:0 -24px 60px -20px rgba(13,42,72,.4), inset 0 1px 0 rgba(255,255,255,.8); }
.install-banner{ background:linear-gradient(145deg,#fff,var(--neo-surface)); box-shadow:var(--neo-raised-lg); }

/* ===== Skeleton tuned to neo base ===== */
.skeleton{ background:#dfe6f0; box-shadow:var(--neo-inset); }

/* Respect reduced motion */
@media (prefers-reduced-motion: reduce){
  .btn,.card-hover,.hover-lift,.hover-scale{ transition:none !important; }
}
</style>
