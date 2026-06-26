<?php

declare(strict_types=1);

namespace App\Core\Container\Config\Contracts;

use App\Core\Container\Config\Types\ConfigType;

interface ConfigInterface
{
    public function getType(): ConfigType;
}
