{{-- Inlined design system — guaranteed to load regardless of asset()/proxy URL.
     Premium, cohesive styling layered on top of Tailwind utilities. --}}
<style>
:root{
  --primary:#2563EB; --primary-700:#1d4ed8; --accent:#10B981; --accent-700:#059669;
  --ink:#0B1220; --ink-soft:#1f2a3d; --muted:#64748B; --bg:#F6F8FC; --card:#FFFFFF;
  --line:rgba(15,23,42,.08); --ring:rgba(37,99,235,.35);
  --shadow-sm:0 1px 2px rgba(16,24,40,.05);
  --shadow:0 4px 16px -4px rgba(16,24,40,.10), 0 2px 6px -2px rgba(16,24,40,.06);
  --shadow-lg:0 24px 48px -16px rgba(16,24,40,.22);
  --shadow-primary:0 14px 30px -10px rgba(37,99,235,.5);
}
*{ -webkit-font-smoothing:antialiased; text-rendering:optimizeLegibility; }
html{ scroll-behavior:smooth; }
body{ background:var(--bg); color:var(--ink); font-family:'Inter',ui-sans-serif,system-ui,sans-serif; font-feature-settings:"cv11","ss01"; letter-spacing:-0.011em; }
.font-display{ font-family:'Plus Jakarta Sans','Inter',sans-serif; letter-spacing:-0.025em; }

/* Tailwind color helpers (work even if CDN config lags) */
.bg-bg{background:var(--bg)} .bg-card{background:var(--card)} .text-ink{color:var(--ink)}
.text-muted{color:var(--muted)} .text-primary{color:var(--primary)} .bg-primary{background:var(--primary)}
.bg-secondary{background:#0F172A} .text-accent{color:var(--accent)} .bg-accent{background:var(--accent)}
.text-success{color:#16a34a} .text-warning{color:#d97706} .text-danger{color:#dc2626}

/* ===== Premium backdrops ===== */
.hero-aurora{ position:relative; background:
  radial-gradient(48% 40% at 12% 0%, rgba(37,99,235,.16), transparent 60%),
  radial-gradient(42% 38% at 92% -5%, rgba(16,185,129,.16), transparent 60%),
  linear-gradient(180deg, #fff 0%, var(--bg) 100%); }
.hero-aurora::before{ content:""; position:absolute; inset:0; background-image:
  radial-gradient(rgba(15,23,42,.045) 1px, transparent 1px); background-size:22px 22px;
  -webkit-mask-image:linear-gradient(180deg,#000,transparent 70%); mask-image:linear-gradient(180deg,#000,transparent 70%); pointer-events:none; }
.text-gradient{ background:linear-gradient(100deg,#2563EB 0%,#10B981 90%); -webkit-background-clip:text; background-clip:text; color:transparent; }

/* ===== Glass ===== */
.glass{ background:rgba(255,255,255,.72); backdrop-filter:saturate(180%) blur(14px); -webkit-backdrop-filter:saturate(180%) blur(14px); border:1px solid rgba(255,255,255,.5); box-shadow:var(--shadow-sm); }

/* ===== Buttons ===== */
.btn{ display:inline-flex; align-items:center; justify-content:center; gap:.5rem; font-weight:600; font-size:.9rem; line-height:1; border-radius:.8rem; padding:.7rem 1.15rem; transition:transform .15s ease, box-shadow .2s ease, background .2s ease, opacity .2s; cursor:pointer; white-space:nowrap; border:1px solid transparent; }
.btn:active{ transform:translateY(0) scale(.98); }
.btn-primary{ background:linear-gradient(180deg,#3b82f6,#2563EB); color:#fff; box-shadow:var(--shadow-primary); }
.btn-primary:hover{ transform:translateY(-1px); box-shadow:0 18px 34px -10px rgba(37,99,235,.6); }
.btn-dark{ background:linear-gradient(180deg,#1f2a3d,#0F172A); color:#fff; box-shadow:var(--shadow); }
.btn-dark:hover{ transform:translateY(-1px); opacity:.95; }
.btn-ghost{ background:transparent; color:var(--ink); }
.btn-ghost:hover{ background:rgba(15,23,42,.06); }

/* ===== Cards ===== */
.card{ background:var(--card); border:1px solid var(--line); border-radius:1.15rem; box-shadow:var(--shadow); }
.card-hover{ transition:transform .22s cubic-bezier(.2,.7,.3,1), box-shadow .22s; }
.card-hover:hover{ transform:translateY(-4px); box-shadow:var(--shadow-lg); }

/* ===== Pills / badges ===== */
.pill{ display:inline-flex; align-items:center; gap:.35rem; font-size:.72rem; font-weight:600; padding:.28rem .6rem; border-radius:999px; line-height:1; }
.pill-cashback{ background:linear-gradient(180deg,rgba(16,185,129,.16),rgba(16,185,129,.1)); color:#047857; border:1px solid rgba(16,185,129,.2); }
.pill-muted{ background:rgba(100,116,139,.1); color:#475569; }

/* ===== Inputs ===== */
.input{ width:100%; border:1px solid var(--line); border-radius:.75rem; padding:.65rem .85rem; font-size:.92rem; background:#fff; color:var(--ink); transition:border-color .15s, box-shadow .15s; }
.input::placeholder{ color:#94a3b8; }
.input:focus{ outline:none; border-color:var(--primary); box-shadow:0 0 0 4px var(--ring); }
select.input{ appearance:none; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='none' stroke='%2364748b' stroke-width='2'%3E%3Cpath d='M4 6l4 4 4-4'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right .7rem center; padding-right:2rem; }

/* ===== Sidebar nav ===== */
.nav-link{ display:flex; align-items:center; gap:.7rem; padding:.6rem .8rem; border-radius:.7rem; color:var(--muted); font-weight:500; font-size:.92rem; transition:.15s; }
.nav-link:hover{ background:rgba(37,99,235,.07); color:var(--ink); }
.nav-link.active{ background:linear-gradient(180deg,rgba(37,99,235,.12),rgba(37,99,235,.08)); color:var(--primary); font-weight:600; }

/* ===== Effects ===== */
.counter{ font-variant-numeric:tabular-nums; }
.skeleton{ position:relative; overflow:hidden; background:#eef2f7; }
.skeleton::after{ content:""; position:absolute; inset:0; transform:translateX(-100%); background:linear-gradient(90deg,transparent,rgba(255,255,255,.7),transparent); animation:shimmer 1.4s infinite; }
@keyframes shimmer{ 100%{ transform:translateX(100%);} }
@keyframes fadeup{ from{ opacity:0; transform:translateY(10px);} to{ opacity:1; transform:none;} }
.fade-up{ animation:fadeup .55s cubic-bezier(.2,.7,.3,1) both; }
[x-cloak]{ display:none !important; }

/* line clamp fallback */
.line-clamp-2{ display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
</style>
