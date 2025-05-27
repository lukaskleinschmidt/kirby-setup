<?php

use Kirby\Cms\App;

App::plugin('project/core', [

    // https://getkirby.com/docs/reference/plugins/extensions

    'commands' => include 'commands.php',
    'hooks'    => include 'hooks.php',

    'blockMethods'        => include 'methods/block.php',
    'blocksMethods'       => include 'methods/blocks.php',
    'collectionMethods'   => include 'methods/collection.php',
    'fieldMethods'        => include 'methods/field.php',
    'fileMethods'         => include 'methods/file.php',
    'filesMethods'        => include 'methods/files.php',
    'layoutColumnMethods' => include 'methods/layoutColumn.php',
    'layoutMethods'       => include 'methods/layout.php',
    'layoutsMethods'      => include 'methods/layouts.php',
    'pageMethods'         => include 'methods/page.php',
    'pagesMethods'        => include 'methods/pages.php',
    'siteMethods'         => include 'methods/site.php',
    'userMethods'         => include 'methods/user.php',
    'usersMethods'        => include 'methods/users.php',

    'templates' => [
        //
    ],

    'snippets' => [
        //
    ],

    'blockModels' => [
        'default' => \App\Core\Block::class,
    ],

    'pageModels' => [
        'default' => \App\Core\Page::class,
        'error'   => \App\Pages\Error::class,
        'home'    => \App\Pages\Home::class,
    ],

    'userModels' => [
        //
    ],

]);
