<?php

namespace App\Core;

use App\Core\App;
use App\Core\Page;
use Closure;

class Menu
{
    public static array $entries = [];

    public static string $path;

    public static function path(): string
    {
        return static::$path ??= App::instance()->request()->path()->toString();
    }

    public static function entry(string $label, string $link, ?string $icon = null, Closure|bool|null $current = null): array
    {
        return static::$entries[] = [
            'label'   => $label,
            'link'    => $link,
            'icon'    => $icon,
            'current' => $current ?? fn () => str_contains(static::path(), $link)
        ];
    }

    public static function page(Page|string $page, ?string $icon = null, Closure|bool|null $current = null): ?array
    {
        if (is_string($page)) {
            $page = App::instance()->page($page);
        }

        if (! $page instanceof Page) {
            return null;
        }

        return static::entry(
            $page->title(),
            $page->panel()->url(true),
            $icon ?? $page->blueprint()->icon() ?? 'page',
            $current
        );
    }

    public static function site(string $label, string $icon = 'home'): array
    {
        return [
            'label'   => $label,
            'icon'    => $icon,
            'current' => function (?string $id = null) {
                if ($id !== 'site') {
                    return false;
                }

                foreach (static::$entries as $entry) {
                    if (str_contains(static::path(), $entry['link'])) {
                        return false;
                    }
                }

                return true;
            },
        ];
    }
}
