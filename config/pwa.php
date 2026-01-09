<?php

return [

    'install-button' => true,

    'manifest' => [
        'name' => 'Laravel 12 PWA',
        'short_name' => 'L12PWA',

        'start_url' => '/product',
        'scope'     => '/',


        'background_color' => '#ffffff',
        'theme_color' => '#0d6efd',
        'display' => 'standalone',
        'description' => 'Laravel 12 Progressive Web Application',

        'icons' => [
            [
                'src' => '/logo.png',
                'sizes' => '512x512',
                'type' => 'image/png',
            ],
        ],
    ],

    'debug' => env('APP_DEBUG', false),

    'livewire-app' => false,
];
