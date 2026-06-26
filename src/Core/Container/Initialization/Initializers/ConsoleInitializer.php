<?php

declare(strict_types=1);

namespace App\Core\Container\Initialization\Initializers;

use App\Core\Console\Discovery\CommandClassFinder;
use App\Core\Console\Metadata\CommandMetadataExtractor;
use App\Core\Container\Config\Loaders\ConsoleLoader;
use App\Core\Container\Initialization\Payload\ConsolePayload;
use App\Core\Container\Initialization\Payload\PayloadInterface;
use App\Core\Utils\PathResolverInterface;

class ConsoleInitializer implements InitializerInterface
{
    public function __construct(
        private readonly PathResolverInterface $pathResolver,
    ) {
    }

    public function initialize(): ?PayloadInterface
    {
        $consoleConfig = new ConsoleLoader($this->pathResolver)->load();
        $commandClasses = new CommandClassFinder($consoleConfig, $this->pathResolver)->scanCommandDirectory();
        $commandMetadata = new CommandMetadataExtractor($commandClasses)->extractCommandsMetadata();

        return new ConsolePayload($consoleConfig, $commandMetadata);
    }
}
