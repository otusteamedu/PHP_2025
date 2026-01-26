<?php

declare(strict_types=1);

namespace Otus\DataMapper\Infrastructure\Config;

interface ConfigInterface
{
    /**
     * @param string $env
     * @param mixed|null $default
     *
     * @return mixed
     */
    public function get(string $env, mixed $default = null): mixed;

    /**
     * @param string $env
     * @param mixed $value
     */
    public function set(string $env, mixed $value): void;
}
