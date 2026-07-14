<?php

declare(strict_types=1);

namespace App\Core\Container\Config\Types;

use App\Core\Container\Config\Contracts\ConfigInterface;

readonly class Console implements ConfigInterface
{
    public function __construct(
        private string $commandDir,
        private string $commandNamespace,
    ) {
    }

    public function getType(): ConfigType
    {
        return ConfigType::CONSOLE;
    }

    public function getCommandDir(): string
    {
        return $this->commandDir;
    }

    public function getCommandNamespace(): string
    {
        return $this->commandNamespace;
    }
}
