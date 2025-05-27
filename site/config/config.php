<?php

header('X-Frame-Options: sameorigin');
header('X-XSS-Protection: 1; mode=block');
header('X-Content-Type-Options: nosniff');

return [

    'url'   => env('APP_URL'),
    'debug' => env('APP_DEBUG', false),

    'panel'  => include __DIR__ . '/panel.php',
    'ready'  => include __DIR__ . '/ready.php',
    'routes' => include __DIR__ . '/routes.php',
    'thumbs' => include __DIR__ . '/thumbs.php',

    'updates' => [
        'plugins' => [
            'project/*' => false,
        ],
    ],

    // 'cache' => [
    //     'pages' => [
    //         'active' => true,
    //     ],
    // ],

];
