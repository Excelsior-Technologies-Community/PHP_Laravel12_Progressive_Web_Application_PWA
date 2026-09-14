"use strict";


/*
|--------------------------------------------------------------------------
| CACHE NAMES
|--------------------------------------------------------------------------
*/

const CACHE_NAME = "laravel-pwa-offline-v3";

const PRODUCT_CACHE_NAME =
    "laravel-pwa-products-v1";


/*
|--------------------------------------------------------------------------
| URLs
|--------------------------------------------------------------------------
*/

const OFFLINE_URL = "/offline.html";

const PRODUCT_URL = "/product";


/*
|--------------------------------------------------------------------------
| INSTALL
|--------------------------------------------------------------------------
*/

self.addEventListener("install", (event) => {

    event.waitUntil(

        caches.open(CACHE_NAME)
            .then((cache) => {

                return cache.add(
                    OFFLINE_URL
                );

            })

    );

    /*
    |--------------------------------------------------------------------------
    | Activate new Service Worker immediately
    |--------------------------------------------------------------------------
    */

    self.skipWaiting();

});


/*
|--------------------------------------------------------------------------
| ACTIVATE
|--------------------------------------------------------------------------
*/

self.addEventListener("activate", (event) => {

    event.waitUntil(

        caches.keys()
            .then((cacheNames) => {

                return Promise.all(

                    cacheNames
                        .filter((cacheName) => {

                            return (

                                cacheName !==
                                    CACHE_NAME

                                &&

                                cacheName !==
                                    PRODUCT_CACHE_NAME

                            );

                        })
                        .map((cacheName) => {

                            return caches.delete(
                                cacheName
                            );

                        })

                );

            })

    );


    /*
    |--------------------------------------------------------------------------
    | Take control of existing pages
    |--------------------------------------------------------------------------
    */

    self.clients.claim();

});


/*
|--------------------------------------------------------------------------
| FETCH
|--------------------------------------------------------------------------
*/

self.addEventListener("fetch", (event) => {

    const request = event.request;


    /*
    |--------------------------------------------------------------------------
    | Only handle GET requests
    |--------------------------------------------------------------------------
    */

    if (request.method !== "GET") {

        return;

    }


    /*
    |--------------------------------------------------------------------------
    | PRODUCT LIST
    |--------------------------------------------------------------------------
    */

    if (
        new URL(request.url).pathname ===
        PRODUCT_URL
    ) {

        event.respondWith(

            /*
            |--------------------------------------------------------------------------
            | Try Network First
            |--------------------------------------------------------------------------
            */

            fetch(request)

                .then((response) => {


                    /*
                    |--------------------------------------------------------------------------
                    | Save successful Product List
                    |--------------------------------------------------------------------------
                    */

                    if (
                        response &&
                        response.status === 200
                    ) {

                        const responseClone =
                            response.clone();


                        caches
                            .open(
                                PRODUCT_CACHE_NAME
                            )
                            .then((cache) => {

                                cache.put(
                                    request,
                                    responseClone
                                );

                            });

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Return latest Product List
                    |--------------------------------------------------------------------------
                    */

                    return response;

                })


                /*
                |--------------------------------------------------------------------------
                | Network Failed
                |--------------------------------------------------------------------------
                */

                .catch(() => {

                    return caches
                        .open(
                            PRODUCT_CACHE_NAME
                        )

                        .then((cache) => {

                            return cache.match(
                                request
                            );

                        })

                        .then((cachedResponse) => {


                            /*
                            |--------------------------------------------------------------------------
                            | Cached Product List exists
                            |--------------------------------------------------------------------------
                            */

                            if (cachedResponse) {

                                return cachedResponse;

                            }


                            /*
                            |--------------------------------------------------------------------------
                            | No Product cache
                            |--------------------------------------------------------------------------
                            */

                            return caches.match(
                                OFFLINE_URL
                            );

                        });

                })

        );


        return;

    }


    /*
    |--------------------------------------------------------------------------
    | OTHER HTML PAGES
    |--------------------------------------------------------------------------
    */

    if (
        request.destination ===
        "document"
    ) {

        event.respondWith(

            fetch(request)

                .catch(() => {

                    return caches.match(
                        OFFLINE_URL
                    );

                })

        );


        return;

    }


    /*
    |--------------------------------------------------------------------------
    | OTHER GET REQUESTS
    |--------------------------------------------------------------------------
    */

    event.respondWith(

        fetch(request)

            .catch(() => {

                return caches.match(
                    request
                );

            })

    );

});