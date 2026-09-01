const CACHE_NAME = 'jmj-wf-pwa-v1';
const STATIC_ASSETS = [
    '/jmj/workforce/mobile',
    '/jmj/workforce/mobile/check-in',
    '/jmj/workforce/mobile/patrol'
];

self.addEventListener('install', (e) => {
    e.waitUntil(
        caches.open(CACHE_NAME).then((cache) => cache.addAll(STATIC_ASSETS)).catch(() => {})
    );
    self.skipWaiting();
});

self.addEventListener('activate', (e) => {
    e.waitUntil(
        caches.keys().then((keys) => {
            return Promise.all(
                keys.map((k) => {
                    if (k !== CACHE_NAME) return caches.delete(k);
                })
            );
        })
    );
    self.clients.claim();
});

self.addEventListener('fetch', (e) => {
    // Network first, fallback to cache for offline capabilities
    if (e.request.method === 'GET') {
        e.respondWith(
            fetch(e.request).catch(() => caches.match(e.request))
        );
    }
});
