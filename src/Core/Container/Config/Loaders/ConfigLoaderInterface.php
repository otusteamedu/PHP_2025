<?php

declare(strict_types=1);

namespace App\Core\Container\Config\Loaders;

use App\Core\Container\Config\Contracts\ConfigInterface;

interface ConfigLoaderInterface
{
    public function load(): ConfigInterface;
}
