// Import OneSignal's service worker SDK
importScripts('https://cdn.onesignal.com/sdks/OneSignalSDKWorker.js');

// Cache only public, session-independent assets. Dynamic pages can contain
// session-specific CSRF tokens and must always come from the network.
const CACHE_NAME = 'offline-v2';
const filesToCache = [
    '/offline.html'
];

// Preload the files for caching
const preLoad = function () {
    return caches.open(CACHE_NAME).then(function (cache) {
        // Caching index and important routes
        return cache.addAll(filesToCache);
    });
};

// Install event for caching resources
self.addEventListener("install", function (event) {
    event.waitUntil(
        preLoad().then(function () {
            return self.skipWaiting();
        })
    );
});

// Use the offline page only when a navigation request cannot reach the server.
// Other requests pass through untouched so authenticated responses and CSRF
// tokens are never persisted by the service worker.
self.addEventListener("fetch", function (event) {
    if (event.request.mode === 'navigate') {
        event.respondWith(
            fetch(event.request).catch(function () {
                return caches.match('/offline.html');
            })
        );
    }
});

// Cache cleanup when a new service worker is activated
self.addEventListener('activate', function(event) {
    const cacheWhitelist = [CACHE_NAME];
    event.waitUntil(
        caches.keys().then(function(cacheNames) {
            return Promise.all(
                cacheNames.map(function(cacheName) {
                    if (!cacheWhitelist.includes(cacheName)) {
                        return caches.delete(cacheName);
                    }
                })
            );
        }).then(function () {
            return self.clients.claim();
        })
    );
});
