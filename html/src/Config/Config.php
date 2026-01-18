<?php

declare(strict_types=1);

namespace Otus\DataMapper\Config;

final class Config implements ConfigInterface
{
    /**
     * @param array $list
     */
    public function __construct(private array $list = [])
    {
    }

    /**
     * @param string $env
     * @param mixed|null $default
     *
     * @return mixed
     */
    public function get(string $env, mixed $default = null): mixed
    {
        return $this->list[$env] ?? $default;
    }

    /**
     * @param string $env
     * @param mixed $value
     */
    public function set(string $env, mixed $value): void
    {
        $this->list[$env] = $value;
    }
}
