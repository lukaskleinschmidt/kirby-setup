<?php

namespace App\Core;

use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;

class Monolog
{
    public Logger $logger;

    protected static Monolog|null $instance = null;

    public function __construct()
    {
        $this->logger = new Logger('kirby');

        $stream = kirby()->root('logs')
            ? kirby()->root('logs') . '/kirby.log'
            : kirby()->root('site') . '/logs/kirby.log';

        $this->logger->pushHandler(new StreamHandler($stream, Level::Debug));
    }

    public static function instance(): Monolog
    {
        return static::$instance ??= new static();
    }

    public static function logger(): Logger
    {
        return static::instance()->logger;
    }

    public static function __callStatic(string $name, array $arguments): mixed
    {
        return static::instance()->logger->{$name}(...$arguments);
    }
}
