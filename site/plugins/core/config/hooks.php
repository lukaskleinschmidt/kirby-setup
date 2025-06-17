<?php

use Dom\HTMLElement;
use Kirby\Cms\File;
use Kirby\Http\Response;
use Kirby\Http\Route;

return [

    // https://getkirby.com/docs/reference/system/options/hooks

    'file.create:after' => function (File $file) {
        if ($file->template()) {
            return;
        }

        $templates = kirby()->blueprints('files');
        $template = $file->type();

        if (! in_array($template, $templates)) {
            $template = null;
        }

        $file->save([
            'template' => $template,
        ]);
    },

    'panel.route:after' => function (Route $route, Response $response) {
        $type = $route->attributes()['type'] ?? 'view';

        if ($type !== 'view') {
            return $response;
        }

        $body = $response->body();

        if ($vite = option('panel.vite')) {
            $body = str_replace('</head>', $vite . '</head>', $body);
        }

        return new Response(
            $body,
            $response->type(),
            $response->code(),
            $response->headers(),
            $response->charset()
        );
    },

];

