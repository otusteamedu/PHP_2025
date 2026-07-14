<?php

declare(strict_types=1);

namespace App\Core\Container\Config\Types;

use App\Core\Container\Config\Contracts\DotEnvConfigInterface;

readonly class DotEnv implements DotEnvConfigInterface
{
    public function __construct(
        private array $data,
    ) {
    }

    public function getType(): ConfigType
    {
        return ConfigType::DOT_ENV;
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
