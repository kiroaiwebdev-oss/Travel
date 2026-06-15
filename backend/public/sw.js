/* TripCash Service Worker — offline support + fast static caching.
   Strategy: network-first for navigations (fresh dynamic pages, offline fallback),
   cache-first for same-origin static assets. Never touches POST or cross-origin. */
const CACHE = 'tc-cache-v1';
const PRECACHE = ['/offline.html', '/icon.svg', '/css/app.css'];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE).then((c) => c.addAll(PRECACHE).catch(() => {})).then(() => self.skipWaiting())
  );
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys()
      .then((keys) => Promise.all(keys.filter((k) => k !== CACHE).map((k) => caches.delete(k))))
      .then(() => self.clients.claim())
  );
});

self.addEventListener('fetch', (event) => {
  const { request } = event;
  if (request.method !== 'GET') return;

  const url = new URL(request.url);
  if (url.origin !== self.location.origin) return; // leave CDNs / images alone

  // HTML navigations -> network first, fall back to offline page
  if (request.mode === 'navigate') {
    event.respondWith(
      fetch(request).catch(() => caches.match('/offline.html'))
    );
    return;
  }

  // Static assets -> cache first, then network (and cache it)
  if (/\.(css|js|png|jpg|jpeg|svg|webp|gif|woff2?)$/i.test(url.pathname)) {
    event.respondWith(
      caches.match(request).then((cached) =>
        cached ||
        fetch(request).then((resp) => {
          const copy = resp.clone();
          caches.open(CACHE).then((c) => c.put(request, copy)).catch(() => {});
          return resp;
        }).catch(() => cached)
      )
    );
  }
});

/* Push notifications (ready for a backend push service) */
self.addEventListener('push', (event) => {
  let data = { title: 'TripCash', body: 'You have an update.' };
  try { data = event.data ? event.data.json() : data; } catch (e) {}
  event.waitUntil(
    self.registration.showNotification(data.title, {
      body: data.body,
      icon: '/icon.svg',
      badge: '/icon.svg',
      data: { url: data.url || '/dashboard' },
    })
  );
});

self.addEventListener('notificationclick', (event) => {
  event.notification.close();
  event.waitUntil(clients.openWindow(event.notification.data.url || '/'));
});
