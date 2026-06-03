var staticCacheName = "pwa-v" + new Date().getTime();
var filesToCache = [
    '/offline',
    '/images/icons/icon-192x192.png',
    '/images/icons/icon-512x512.png',
];

// Cache on install
self.addEventListener("install", event => {
    this.skipWaiting();
    event.waitUntil(
        caches.open(staticCacheName)
            .then(cache => {
                return cache.addAll(filesToCache);
            })
    )
});

// Clear cache on activate
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames
                    .filter(cacheName => (cacheName.startsWith("pwa-")))
                    .filter(cacheName => (cacheName !== staticCacheName))
                    .map(cacheName => caches.delete(cacheName))
            );
        })
    );
});

// Serve from Cache (only GET requests)
self.addEventListener("fetch", event => {
    if (event.request.method !== 'GET') return;

    event.respondWith(
        caches.match(event.request)
            .then(response => {
                return response || fetch(event.request);
            })
            .catch(() => {
                return caches.match('offline');
            })
    );
});

// Recibir notificación push y mostrarla
self.addEventListener('push', event => {
    if (!event.data) return;

    let title = 'Sistema Dental';
    let body  = '';
    let url   = '/';

    try {
        const data = event.data.json();
        title = data.title || title;
        body  = data.body  || body;
        url   = data.url   || url;
    } catch (_) {
        // El mensaje era texto plano, no JSON
        body = event.data.text();
    }

    event.waitUntil(
        self.registration.showNotification(title, {
            body:  body,
            icon:  '/images/icons/icon-192x192.png',
            badge: '/images/icons/icon-72x72.png',
            data:  { url: url },
        })
    );
});

// Al hacer clic en la notificación, abrir la URL
self.addEventListener('notificationclick', event => {
    event.notification.close();
    event.waitUntil(
        clients.openWindow(event.notification.data.url)
    );
});