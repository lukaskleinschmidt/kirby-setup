<?php

use App\Core\App;
use App\Core\Env;

define('KIRBY_HELPER_DUMP', false);

// Register the Composer autoloader
require __DIR__ . '/../vendor/autoload.php';

// Load the environment variables
Env::load([
    $base = dirname(__DIR__),
]);

// Create the Kirby application
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
