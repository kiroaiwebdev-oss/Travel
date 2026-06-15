{{-- Inlined premium design system (always loads, proxy-safe).
     brand/travel = teal-emerald · payment/CTA = blue · deals = orange · cashback = green --}}
<style>
:root{
  --brand:#0d9488; --brand-600:#0d9488; --brand-700:#0f766e; --brand-500:#10b981;
  --pay:#2563EB; --pay-700:#1d4ed8; --deal:#F97316; --cash:#059669;
  --ink:#0B1220; --muted:#64748B; --bg:#F7F9FC; --card:#FFFFFF;
  --line:rgba(15,23,42,.08); --line-2:rgba(15,23,42,.06);
  --shadow-sm:0 1px 2px rgba(16,24,40,.05);
  --shadow:0 6px 22px -8px rgba(16,24,40,.14), 0 2px 6px -2px rgba(16,24,40,.06);
  --shadow-lg:0 30px 60px -22px rgba(13,42,72,.30);
  --shadow-brand:0 16px 32px -12px rgba(13,148,136,.5);
  --shadow-pay:0 16px 32px -12px rgba(37,99,235,.5);
}
*{ -webkit-font-smoothing:antialiased; text-rendering:optimizeLegibility; }
html{ scroll-behavior:smooth; }
body{ background:var(--bg); color:var(--ink); font-family:'Inter',ui-sans-serif,system-ui,sans-serif; letter-spacing:-0.011em; }
.font-display{ font-family:'Plus Jakarta Sans','Inter',sans-serif; letter-spacing:-0.03em; }

/* color helpers */
.bg-bg{background:var(--bg)} .bg-card{background:var(--card)} .text-ink{color:var(--ink)}
.text-muted{color:var(--muted)} .text-brand{color:var(--brand-700)} .bg-brand{background:var(--brand)}
.text-pay{color:var(--pay)} .bg-pay{background:var(--pay)} .text-deal{color:var(--deal)}
.text-accent{color:var(--brand-500)} .bg-secondary{background:#0B1220}
.text-success{color:#16a34a} .text-warning{color:#d97706} .text-danger{color:#dc2626}

/* ===== Premium hero backdrop ===== */
.hero-aurora{ position:relative; overflow:hidden; background:
  radial-gradient(60% 50% at 8% -10%, rgba(13,148,136,.20), transparent 55%),
  radial-gradient(55% 50% at 100% -10%, rgba(37,99,235,.16), transparent 55%),
  radial-gradient(40% 40% at 60% 0%, rgba(16,185,129,.12), transparent 60%),
  linear-gradient(180deg,#ffffff 0%, var(--bg) 70%); }
.hero-aurora::before{ content:""; position:absolute; inset:0;
  background-image:linear-gradient(rgba(15,23,42,.04) 1px,transparent 1px),linear-gradient(90deg,rgba(15,23,42,.04) 1px,transparent 1px);
  background-size:34px 34px; -webkit-mask-image:radial-gradient(70% 55% at 50% 0%,#000,transparent 75%); mask-image:radial-gradient(70% 55% at 50% 0%,#000,transparent 75%); pointer-events:none; }
.text-gradient{ background:linear-gradient(100deg,var(--brand) 0%, var(--brand-500) 45%, var(--pay) 110%); -webkit-background-clip:text; background-clip:text; color:transparent; }

/* ===== Glass ===== */
.glass{ background:rgba(255,255,255,.70); backdrop-filter:saturate(180%) blur(16px); -webkit-backdrop-filter:saturate(180%) blur(16px); border:1px solid rgba(255,255,255,.6); }

/* ===== Buttons ===== */
.btn{ display:inline-flex; align-items:center; justify-content:center; gap:.5rem; font-weight:600; font-size:.9rem; line-height:1; border-radius:.85rem; padding:.72rem 1.2rem; transition:transform .15s ease, box-shadow .22s ease, background .2s, opacity .2s; cursor:pointer; white-space:nowrap; border:1px solid transparent; }
.btn:active{ transform:translateY(0) scale(.985); }
/* payment / primary CTA = blue */
.btn-primary,.btn-pay{ background:linear-gradient(180deg,#3b82f6,#2563EB); color:#fff; box-shadow:var(--shadow-pay); }
.btn-primary:hover,.btn-pay:hover{ transform:translateY(-1px); box-shadow:0 20px 38px -12px rgba(37,99,235,.62); }
/* travel action = brand teal */
.btn-brand{ background:linear-gradient(180deg,#14b8a6,#0d9488); color:#fff; box-shadow:var(--shadow-brand); }
.btn-brand:hover{ transform:translateY(-1px); box-shadow:0 20px 38px -12px rgba(13,148,136,.6); }
.btn-dark{ background:linear-gradient(180deg,#1f2a3d,#0B1220); color:#fff; box-shadow:var(--shadow); }
.btn-dark:hover{ transform:translateY(-1px); opacity:.95; }
.btn-ghost{ background:transparent; color:var(--ink); }
.btn-ghost:hover{ background:rgba(15,23,42,.05); }
.btn-white{ background:#fff; color:var(--ink); box-shadow:var(--shadow); }
.btn-white:hover{ transform:translateY(-1px); }

/* ===== Cards ===== */
.card{ background:var(--card); border:1px solid var(--line-2); border-radius:1.2rem; box-shadow:var(--shadow); }
.card-hover{ transition:transform .24s cubic-bezier(.2,.7,.3,1), box-shadow .24s, border-color .24s; }
.card-hover:hover{ transform:translateY(-5px); box-shadow:var(--shadow-lg); border-color:rgba(13,148,136,.25); }

/* ===== Pills / badges ===== */
.pill{ display:inline-flex; align-items:center; gap:.35rem; font-size:.72rem; font-weight:700; padding:.3rem .6rem; border-radius:999px; line-height:1; letter-spacing:.01em; }
.pill-cashback{ background:linear-gradient(180deg,rgba(5,150,105,.16),rgba(5,150,105,.1)); color:#047857; border:1px solid rgba(5,150,105,.22); }
.pill-deal{ background:linear-gradient(180deg,rgba(249,115,22,.18),rgba(249,115,22,.1)); color:#c2410c; border:1px solid rgba(249,115,22,.25); }
.pill-brand{ background:rgba(13,148,136,.1); color:var(--brand-700); border:1px solid rgba(13,148,136,.2); }
.pill-muted{ background:rgba(100,116,139,.1); color:#475569; }

/* ===== Inputs ===== */
.input{ width:100%; border:1px solid var(--line); border-radius:.8rem; padding:.7rem .9rem; font-size:.92rem; background:#fff; color:var(--ink); transition:border-color .15s, box-shadow .15s; }
.input::placeholder{ color:#94a3b8; }
.input:focus{ outline:none; border-color:var(--brand); box-shadow:0 0 0 4px rgba(13,148,136,.18); }
select.input{ appearance:none; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='none' stroke='%2364748b' stroke-width='2'%3E%3Cpath d='M4 6l4 4 4-4'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right .7rem center; padding-right:2rem; }

/* ===== Sidebar nav ===== */
.nav-link{ display:flex; align-items:center; gap:.7rem; padding:.62rem .82rem; border-radius:.7rem; color:var(--muted); font-weight:500; font-size:.92rem; transition:.15s; }
.nav-link:hover{ background:rgba(13,148,136,.08); color:var(--ink); }
.nav-link.active{ background:linear-gradient(180deg,rgba(13,148,136,.14),rgba(13,148,136,.08)); color:var(--brand-700); font-weight:600; }

/* ===== Effects ===== */
.counter{ font-variant-numeric:tabular-nums; }
.ring-grid{ background-image:radial-gradient(rgba(15,23,42,.05) 1px,transparent 1px); background-size:22px 22px; }
.skeleton{ position:relative; overflow:hidden; background:#eef2f7; }
.skeleton::after{ content:""; position:absolute; inset:0; transform:translateX(-100%); background:linear-gradient(90deg,transparent,rgba(255,255,255,.7),transparent); animation:shimmer 1.4s infinite; }
@keyframes shimmer{ 100%{ transform:translateX(100%);} }
@keyframes fadeup{ from{ opacity:0; transform:translateY(12px);} to{ opacity:1; transform:none;} }
.fade-up{ animation:fadeup .6s cubic-bezier(.2,.7,.3,1) both; }
.fade-up-2{ animation:fadeup .6s .08s cubic-bezier(.2,.7,.3,1) both; }
.fade-up-3{ animation:fadeup .6s .16s cubic-bezier(.2,.7,.3,1) both; }
[x-cloak]{ display:none !important; }
.line-clamp-2{ display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
</style>
