<?php

declare(strict_types=1);

namespace App\Domain\Interfaces;

interface ConfigInterface
{
    public function get(string $key, $default = null);
    public function has(string $key): bool;
}
