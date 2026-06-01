<?php

declare(strict_types=1);

namespace App\Core\Container\Config\Loaders;

enum ConfigLoaderType: string
{
    case CONSOLE = 'console';
    case MODULES = 'modules';

    public function getLoaderClass(): string
    {
        return match ($this) {
            self::CONSOLE => ConsoleConfigLoader::class,
            self::MODULES => ModuleConfigLoader::class,
        };
    }
}
