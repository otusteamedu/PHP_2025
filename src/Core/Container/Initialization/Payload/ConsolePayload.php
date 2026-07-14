<?php

declare(strict_types=1);

namespace App\Core\Container\Initialization\Payload;

use App\Core\Console\Metadata\CommandMetadata;
use App\Core\Container\Config\Contracts\ConfigInterface;
use App\Core\Container\Config\Types\Console;

readonly class ConsolePayload implements PayloadInterface
{
    /**
     * @param Console $consoleConfig
     * @param CommandMetadata[] $commandMetadata
     */
    public function __construct(
        public ConfigInterface $consoleConfig,
        public array $commandMetadata,
    ) {
    }
}
