<?php

use App\Core\App;
use App\Core\Menu;

return [

    // https://getkirby.com/docs/reference/system/options/panel

    'menu' => function (App $kirby) {
        $pages = $kirby->user()?->favorites()->toPages()->map(fn ($page) =>
            Menu::page($page)
        )->data();

        if (count($pages ?? []) === 0) {
            $pages['home'] = Menu::page('home');
        }

        return [
            'site' => Menu::site('Site'),
            '-',
            ...$pages,
            '-',
            'users',
            'languages',
            'system',
        ];
    },

    'vite' => vite([
        'resources/scripts/panel.js',
        'resources/styles/panel.css',
    ]),

];
