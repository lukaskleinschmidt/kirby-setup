<?php

use App\Core\App;
use Dotenv\Dotenv;

define('KIRBY_HELPER_DUMP', false);

require dirname(__DIR__) . '/vendor/autoload.php';

/**
 * Load Environment Variables
 */
Dotenv::createImmutable($base = dirname(__DIR__))->safeLoad();

/**
 * Create The Application
 */
$app = new App([
    'roots' => [
        'base'     => $base,
        'index'    => $base . '/public',
        'content'  => $base . '/content',
        'site'     => $base . '/site',
        'storage'  => $storage = $base . '/storage',
        'accounts' => $storage . '/accounts',
        'cache'    => $storage . '/cache',
        'sessions' => $storage . '/sessions',
        'logs'     => $storage . '/logs',
    ],
]);

echo $app->render();
