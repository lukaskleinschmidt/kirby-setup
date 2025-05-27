<?php

namespace App\Core;

use Dotenv\Repository\Adapter\PutenvAdapter;
use Dotenv\Repository\RepositoryBuilder;
use Dotenv\Repository\RepositoryInterface;
use PhpOption\Option;
use PhpOption\Some;
use RuntimeException;

class Env
{
    /**
     * Indicates if the putenv adapter is enabled.
     */
    protected static bool $putenv = true;

    /**
     * The environment repository instance.
     */
    protected static ?RepositoryInterface $repository = null;

    /**
     * Enable the putenv adapter.
     */
    public static function enablePutenv(): void
    {
        static::$putenv = true;
        static::$repository = null;
    }

    /**
     * Disable the putenv adapter.
     */
    public static function disablePutenv(): void
    {
        static::$putenv = false;
        static::$repository = null;
    }

    /**
     * Get the environment repository instance.
     */
    public static function getRepository(): RepositoryInterface
    {
        if (static::$repository === null) {
            $builder = RepositoryBuilder::createWithDefaultAdapters();

            if (static::$putenv) {
                $builder = $builder->addAdapter(PutenvAdapter::class);
            }

            static::$repository = $builder->immutable()->make();
        }

        return static::$repository;
    }

    /**
     * Get the value of an environment variable.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return self::getOption($key)->getOrCall(fn () => value($default));
    }

    /**
     * Get the value of a required environment variable.
     *
     * @throws \RuntimeException
     */
    public static function getOrFail(string $key): mixed
    {
        return self::getOption($key)->getOrThrow(new RuntimeException("Environment variable [$key] has no value."));
    }

    /**
     * Get the possible option for this environment variable.
     */
    protected static function getOption(string $key): Option|Some
    {
        return Option::fromValue(static::getRepository()->get($key))
            ->map(function ($value) {
                switch (strtolower($value)) {
                    case 'true':
                    case '(true)':
                        return true;
                    case 'false':
                    case '(false)':
                        return false;
                    case 'empty':
                    case '(empty)':
                        return '';
                    case 'null':
                    case '(null)':
                        return;
                }

                if (preg_match('/\A([\'"])(.*)\1\z/', $value, $matches)) {
                    return $matches[2];
                }

                return $value;
            });
    }
}
