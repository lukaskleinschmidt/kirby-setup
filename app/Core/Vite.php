<?php

namespace App\Core;

use Kirby\Cms\Url;
use Kirby\Data\Data;
use Kirby\Filesystem\F;
use Kirby\Toolkit\Html;
use Kirby\Toolkit\Str;
use Stringable;

class Vite implements Stringable
{
    protected string|false $host;

    protected array $manifest;

    public function __construct(
        public array $entries = [],
    ) {}

    public function __toString(): string
    {
        $entries = $this->resolve($this->entries);

        $entries = array_values($entries);
        $entries = array_merge(...$entries);

        return implode('', $entries);
    }

    public function dev(): bool
    {
        return $this->host() !== false;
    }

    public function host(): string|false
    {
        return $this->host ??= F::read(
            file: kirby()->root('index') . '/vite'
        );
    }

    public function manifest(): array
    {
        return $this->manifest ??= Data::read(
            file: kirby()->root('index') . '/build/manifest.json',
            fail: false
        );
    }

    public function asset(string $path, mixed $default = null): mixed
    {
        if ($this->dev()) {
            return $this->url($path);
        }

        $manifest = $this->manifest();

        if ($file = $manifest[$path]['file'] ?? null) {
            return $this->url($file);
        }

        return $default;
    }

    public function resolve(?array $entries = null): array
    {
        $entries ??= $this->entries;

        $preloads = [];
        $scripts  = [];
        $styles   = [];
        $vite     = [];

        if ($this->dev()) {
            $url        = $this->url('@vite/client');
            $vite[$url] = $this->script($url);
        }

        foreach ($entries as $file) {
            $file = $this->file($file);

            if (is_null($file)) {
                continue;
            }

            $this->dev()
                ? $this->resolveFile($file, $scripts, $styles)
                : $this->resolveProd($file, $styles, $scripts, $preloads);
        }

        return [
            'vite'     => $vite,
            'styles'   => $styles,
            'scripts'  => $scripts,
            'preloads' => $preloads,
        ];
    }

    protected function resolveProd(string $entry, array &$styles, array &$scripts, array &$preloads): void
    {
        $manifest = $this->manifest();

        $chunk = $manifest[$entry] ?? false;

        if ($chunk === false) {
            return;
        }

        foreach ($chunk['css'] ?? [] as $file) {
            $this->resolveFile($file, $scripts, $styles);
        }

        foreach ($chunk['imports'] ?? [] as $file) {
            $imports = [];

            if ($preload = $manifest[$file]['file'] ?? null) {
                $url            = $this->url($preload);
                $preloads[$url] = $this->preload($url);
            }

            $this->resolveProd($file, $styles, $imports, $preloads);
        }

        if ($file = $chunk['file'] ?? null) {
            $this->resolveFile($file, $scripts, $styles);
        }
    }

    protected function resolveFile(string $file, array &$scripts, array &$styles): void
    {
        if (preg_match('/\.(js|ts)$/', $file)) {
            $url           = $this->url($file);
            $scripts[$url] = $this->script($url);
        }

        if (preg_match('/\.(css|scss|sass)$/', $file)) {
            $url          = $this->url($file);
            $styles[$url] = $this->style($url);
        }
    }

    protected function file(string $entry): ?string
    {
        $file = Str::template($entry, [
            'kirby' => $kirby = kirby(),
            'site'  => $kirby->site(),
            'page'  => $kirby->site()->page(),
        ]);

        if ($optional = str_starts_with($file, '@')) {
            $file = substr($file, 1);
        }

        if ($optional || $file !== $entry) {
            return $this->exists($file) ? $file : null;
        }

        return $file;
    }

    protected function exists(string $file): bool
    {
        return file_exists(kirby()->root('base') . '/' . $file);
    }

    protected function url(string $path): string
    {
        if ($host = $this->host()) {
            return Url::build(['path' => $path], $host);
        }

        return Url::to('/build/' . $path);
    }

    public function script(string $src): string
    {
        return Html::tag('script', attr: [
            'type' => 'module',
            'src'  => $src,
        ]);
    }

    public function style(string $href): string
    {
        return Html::tag('link', attr: [
            'rel'  => 'stylesheet',
            'href' => $href,
        ]);
    }

    public function preload(string $href): string
    {
        return Html::tag('link', attr: [
            'rel'  => 'modulepreload',
            'href' => $href,
        ]);
    }

    public function react(): string
    {
        if ($this->dev() === false) {
            return '';
        }

        $url = $this->url('@react-refresh');

        $value = <<<JS

            import RefreshRuntime from $url
            RefreshRuntime.injectIntoGlobalHook(window)
            window.\$RefreshReg$ = () => {}
            window.\$RefreshSig$ = () => (type) => type
            window.__vite_plugin_react_preamble_installed__ = true

        JS;

        return Html::tag('script', ['value' => $value], ['type' => 'module']);
    }
}
