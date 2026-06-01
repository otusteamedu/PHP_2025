<?php

declare(strict_types=1);

namespace App\Core\Container\Config\Data\DotEnv;

class DotEnvConfig implements DotEnvConfigInterface
{
    public function __construct(
        private readonly array $data,
    ) {
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->data[$key] ?? $default;
    }

    public function has(string $key): bool
    {
        return isset($this->data[$key]);
    }
}
