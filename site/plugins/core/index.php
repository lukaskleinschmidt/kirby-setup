<?php

use Kirby\Cms\App;

App::plugin('project/core', [

    // https://getkirby.com/docs/reference/plugins/extensions

    'commands' => include 'config/commands.php',
    'hooks'    => include 'config/hooks.php',

    'blockMethods'        => include 'config/methods/block.php',
    'blocksMethods'       => include 'config/methods/blocks.php',
    'collectionMethods'   => include 'config/methods/collection.php',
    'fieldMethods'        => include 'config/methods/field.php',
    'fileMethods'         => include 'config/methods/file.php',
    'filesMethods'        => include 'config/methods/files.php',
    'layoutColumnMethods' => include 'config/methods/layoutColumn.php',
    'layoutMethods'       => include 'config/methods/layout.php',
    'layoutsMethods'      => include 'config/methods/layouts.php',
    'pageMethods'         => include 'config/methods/page.php',
    'pagesMethods'        => include 'config/methods/pages.php',
    'siteMethods'         => include 'config/methods/site.php',
    'userMethods'         => include 'config/methods/user.php',
    'usersMethods'        => include 'config/methods/users.php',

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
        'default' => \App\Core\User::class,
    ],

]);
