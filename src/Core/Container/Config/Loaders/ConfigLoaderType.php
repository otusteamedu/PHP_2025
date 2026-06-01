<?php

declare(strict_types=1);

namespace App\Core\Container\Config\Loaders;

enum ConfigLoaderType: string
{
    case MODULES = 'modules';

    public function getLoaderClass(): string
    {
        return match ($this) {
            self::MODULES => ModuleConfigLoader::class,
        };
    }
}
