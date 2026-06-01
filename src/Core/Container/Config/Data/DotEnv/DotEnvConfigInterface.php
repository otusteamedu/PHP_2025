<?php

declare(strict_types=1);

namespace App\Core\Container\Config\Data\DotEnv;

use App\Core\Container\Config\Data\ConfigInterface;

interface DotEnvConfigInterface extends ConfigInterface
{
    public function get(string $key, mixed $default = null): mixed;

    public function has(string $key): bool;
}
