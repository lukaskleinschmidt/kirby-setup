<?php

use App\Core\Env;
use App\Core\Monolog;
use App\Core\Vite;
use Kirby\Content\Field;
use Kirby\Template\Snippet;
use Monolog\Logger;

function env($key, $default = null)
{
    return Env::get($key, $default);
}

function layout(string $name = 'default', ?array $data = []): Snippet
{
	return Snippet::begin(
        kirby()->root('site') . '/layouts/' . $name . '.php',
        $data
    );
}

function monolog(Stringable|string|null $message = null, array $context = []): ?Logger
{
    if (is_null($message)) {
        return Monolog::logger();
    }

    if ($message instanceof Throwable) {
        return Monolog::error($message->getMessage(), [
            'file'  => $message->getFile(),
            'line'  => $message->getLine(),
            'trace' => $message->getTrace(),
        ]);
    }

    return Monolog::logger()->debug($message, $context);
}

function value(mixed $value, mixed ...$args): mixed
{
    if ($value instanceof Field) {
        $value = $value->value();
    }

    return $value instanceof Closure ? $value(...$args) : $value;
}

function vite(array|string|null $entries = null)
{
    if (is_null($entries)) {
        return new Vite();
    }

    return new Vite()->entries((array) $entries);
}
