"use strict";

const CACHE_NAME = "laravel-pwa-offline-v2";
const OFFLINE_URL = "/offline.html";

// Install – cache offline page
self.addEventListener("install", (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => cache.add(OFFLINE_URL))
    );
    self.skipWaiting();
});

// Activate
self.addEventListener("activate", (event) => {
    event.waitUntil(self.clients.claim());
});

// Fetch – FORCE offline page when network fails
self.addEventListener("fetch", (event) => {

    if (event.request.destination === "document") {
        event.respondWith(
            fetch(event.request)
                .then(response => response)
                .catch(() => caches.match(OFFLINE_URL))
        );
    }
});
