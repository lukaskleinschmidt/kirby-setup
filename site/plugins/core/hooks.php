<?php

use Kirby\Cms\File;

return [
    // https://getkirby.com/docs/reference/system/options/hooks

    'file.create:after' => function (File $file)
    {
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
];

