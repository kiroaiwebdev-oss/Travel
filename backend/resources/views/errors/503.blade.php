<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Be right back — {{ config('app.name', 'TripCash') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{min-height:100vh;display:grid;place-items:center;padding:24px;font-family:'Inter',system-ui,sans-serif;color:#1E293B;
             background:radial-gradient(60% 50% at 12% -10%,rgba(0,184,169,.18),transparent 55%),radial-gradient(55% 50% at 100% -10%,rgba(15,98,254,.16),transparent 55%),linear-gradient(180deg,#fff 0%,#F8FAFC 70%)}
        .card{max-width:460px;width:100%;text-align:center;background:rgba(255,255,255,.75);backdrop-filter:blur(16px);
              border:1px solid rgba(255,255,255,.6);border-radius:24px;padding:44px 32px;box-shadow:0 30px 60px -22px rgba(13,42,72,.30)}
        .logo{display:inline-flex;align-items:center;gap:8px;font-family:'Plus Jakarta Sans';font-weight:800;font-size:18px;margin-bottom:22px}
        .logo span.mark{display:grid;place-items:center;width:34px;height:34px;border-radius:11px;color:#fff;background:linear-gradient(150deg,#14b8a6,#0d9488);font-size:18px}
        .big{font-family:'Plus Jakarta Sans';font-weight:800;font-size:64px;line-height:1.1;background:linear-gradient(100deg,#00B8A9,#0F62FE);-webkit-background-clip:text;background-clip:text;color:transparent}
        h1{font-family:'Plus Jakarta Sans';font-weight:800;font-size:24px;margin:12px 0 8px}
        p{color:#64748B;font-size:15px;line-height:1.6;margin-bottom:24px}
        a.btn{display:inline-flex;align-items:center;gap:7px;font-weight:700;font-size:14px;border-radius:13px;padding:12px 20px;text-decoration:none;
              background:linear-gradient(180deg,#3b82f6,#0F62FE);color:#fff;box-shadow:0 16px 32px -12px rgba(15,98,254,.5)}
        .foot{margin-top:24px;font-size:12px;color:#94a3b8}
    </style>
</head>
<body>
    <div class="card">
        <div class="logo"><span class="mark">✈</span> Trip<span style="color:#00B8A9">Cash</span></div>
        <div class="big">Be right back</div>
        <h1>We're doing quick maintenance</h1>
        <p>{{ config('app.name', 'TripCash') }} is briefly down for an upgrade. We'll be back in a few minutes — thanks for your patience!</p>
        <a class="btn" href="{{ url('/') }}">↻ Reload homepage</a>
        <p class="foot">{{ config('app.name', 'TripCash') }} — travel more, earn cashback</p>
    </div>
</body>
</html>
