<?php

declare(strict_types=1);

namespace App\Core\Container\Config\Data\Console;

use App\Core\Container\Config\Data\ConfigInterface;

readonly class ConsoleConfig implements ConfigInterface
{
    public function __construct(
        private string $commandDir,
        private string $commandNamespace,
    ) {
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
