<?php

declare(strict_types=1);

namespace App\Core\Container\Config\Loaders;

enum ConfigLoaderType: string
{
    case DOT_ENV = '.env';
    case CONSOLE = 'console';
    case MODULES = 'modules';

    public function getLoaderClass(): string
    {
        return match ($this) {
            self::DOT_ENV => DotEnvConfigLoader::class,
            self::CONSOLE => ConsoleConfigLoader::class,
            self::MODULES => ModuleConfigLoader::class,
        };
    }
}
