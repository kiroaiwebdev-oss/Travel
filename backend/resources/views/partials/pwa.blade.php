{{-- PWA: installable, standalone, offline-capable, push-ready.
     Favicon + manifest are dynamic so admin-uploaded branding (site.icon) applies everywhere. --}}
@php $brandIcon = \App\Models\Setting::get('site.icon'); @endphp
<link rel="manifest" href="{{ route('pwa.manifest') }}">
<meta name="theme-color" content="#0F62FE" media="(prefers-color-scheme: light)">
<meta name="theme-color" content="#0B1220" media="(prefers-color-scheme: dark)">
<link rel="icon" type="image/svg+xml" href="{{ $brandIcon ?: '/icon.svg' }}">
<link rel="icon" sizes="192x192" href="{{ $brandIcon ?: '/icons/icon-192.png' }}">
<link rel="apple-touch-icon" href="{{ $brandIcon ?: '/icons/icon-192.png' }}">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="TripCash">

{{-- Early: flag standalone (installed-app) mode so CSS/JS can adapt before paint --}}
<script>
  (function () {
    var standalone = window.matchMedia('(display-mode: standalone)').matches
      || window.matchMedia('(display-mode: minimal-ui)').matches
      || window.navigator.standalone === true;
    if (standalone) document.documentElement.classList.add('standalone');
  })();
</script>

{{-- Service worker registration --}}
<script>
  if ('serviceWorker' in navigator) {
    window.addEventListener('load', function () {
      navigator.serviceWorker.register('/sw.js').catch(function () {});
    });
  }
</script>

{{-- "Add to Home Screen" install banner (Android/Chrome) + iOS hint --}}
<script>
  (function () {
    if (document.documentElement.classList.contains('standalone')) return;
    var DISMISS_KEY = 'tc_install_dismissed';
    if (localStorage.getItem(DISMISS_KEY) === '1') return;

    var deferredPrompt = null;

    function buildBanner(isIOS) {
      var bar = document.createElement('div');
      bar.className = 'install-banner';
      bar.style.opacity = '0';
      bar.style.transform = 'translateY(12px)';
      bar.style.transition = 'opacity .3s ease, transform .3s ease';
      bar.innerHTML =
        '<div style="display:flex;align-items:center;gap:.75rem">' +
          '<span style="width:2.6rem;height:2.6rem;border-radius:.8rem;display:grid;place-items:center;color:#fff;background:linear-gradient(150deg,#14b8a6,#0d9488);flex:0 0 auto">' +
            '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.8 19.2 16 11l3.5-3.5C21 6 21.5 3 21 3c-.5-.5-3.5 0-4.5 1.5L13 8 4.8 6.2c-.5-.1-.9.1-1.1.5l-.3.5c-.2.5-.1 1 .3 1.3L9 12l-2 3H4l-1 1 3 2 2 3 1-1v-3l3-2 3.5 5.3c.3.4.8.5 1.3.3l.5-.2c.4-.3.6-.7.5-1.2z"/></svg>' +
          '</span>' +
          '<div style="flex:1;min-width:0">' +
            '<p style="font-weight:700;font-size:.9rem;color:#1E293B">Install TripCash</p>' +
            '<p style="font-size:.76rem;color:#64748B">' + (isIOS ? 'Tap Share then "Add to Home Screen"' : 'Add to home screen for the full app') + '</p>' +
          '</div>' +
          (isIOS ? '' : '<button id="tc-install-btn" class="btn btn-brand" style="padding:.5rem .9rem;font-size:.8rem">Install</button>') +
          '<button id="tc-install-x" aria-label="Dismiss" style="width:2rem;height:2rem;border-radius:999px;display:grid;place-items:center;color:#94a3b8;flex:0 0 auto">' +
            '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M18 6 6 18M6 6l12 12"/></svg>' +
          '</button>' +
        '</div>';
      document.body.appendChild(bar);
      requestAnimationFrame(function () { bar.style.opacity = '1'; bar.style.transform = 'none'; });

      function close() { bar.style.opacity = '0'; bar.style.transform = 'translateY(12px)'; setTimeout(function(){ bar.remove(); }, 300); }
      bar.querySelector('#tc-install-x').addEventListener('click', function () { localStorage.setItem(DISMISS_KEY, '1'); close(); });
      var btn = bar.querySelector('#tc-install-btn');
      if (btn) btn.addEventListener('click', function () {
        if (!deferredPrompt) return;
        deferredPrompt.prompt();
        deferredPrompt.userChoice.finally(function () { deferredPrompt = null; localStorage.setItem(DISMISS_KEY, '1'); close(); });
      });
      return bar;
    }

    window.addEventListener('beforeinstallprompt', function (e) {
      e.preventDefault();
      deferredPrompt = e;
      if (document.body) buildBanner(false);
      else window.addEventListener('DOMContentLoaded', function () { buildBanner(false); });
    });

    // iOS Safari has no beforeinstallprompt — show a manual hint once.
    var isIOS = /iphone|ipad|ipod/i.test(window.navigator.userAgent) && !window.MSStream;
    if (isIOS) {
      window.addEventListener('load', function () { setTimeout(function () { buildBanner(true); }, 2500); });
    }
  })();
</script>
