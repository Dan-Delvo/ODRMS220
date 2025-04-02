// Import OneSignal's service worker SDK
importScripts('https://cdn.onesignal.com/sdks/OneSignalSDKWorker.js');

// Cache name and files to cache for offline functionality
const CACHE_NAME = 'offline-v1';
const filesToCache = [
    '/',
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
    event.waitUntil(preLoad());
});

// Check if the response is available from the network
const checkResponse = function (request) {
    return new Promise(function (fulfill, reject) {
        fetch(request).then(function (response) {
            if (response.status !== 404) {
                fulfill(response);
            } else {
                reject();
            }
        }, reject);
    });
};

// Add successful responses to the cache
const addToCache = function (request) {
    return caches.open(CACHE_NAME).then(function (cache) {
        return fetch(request).then(function (response) {
            return cache.put(request, response);
        });
    });
};

// Return the cached response or fallback to offline.html
const returnFromCache = function (request) {
    return caches.open(CACHE_NAME).then(function (cache) {
        return cache.match(request).then(function (matching) {
            if (!matching || matching.status === 404) {
                return cache.match("offline.html");
            } else {
                return matching;
            }
        });
    });
};

// Fetch event listener to handle network requests and fallback to cache
self.addEventListener("fetch", function (event) {
    event.respondWith(checkResponse(event.request).catch(function () {
        return returnFromCache(event.request);
    }));

    // Cache all HTTP requests except offline.html
    if (event.request.url.startsWith('http') && !event.request.url.includes('offline.html')) {
        event.waitUntil(addToCache(event.request));
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
        })
    );
});
