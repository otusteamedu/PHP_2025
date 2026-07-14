<?php

declare(strict_types=1);

namespace App\Core\Console\Discovery;

use App\Core\Container\Config\Contracts\ConfigInterface;
use App\Core\Container\Config\Types\Console;
use App\Core\Utils\PathResolverInterface;
use Symfony\Component\Console\Command\Command;

class CommandClassFinder
{
    /**
     * @param Console $consoleConfig
     */
    public function __construct(
        private readonly ConfigInterface $consoleConfig,
        private readonly PathResolverInterface $pathResolver,
    ) {
    }

    /**
     * @return string[] command class names
     */
    public function scanCommandDirectory(): array
    {
        $commandDir = "{$this->pathResolver->getSrcPath()}/{$this->consoleConfig->getCommandDir()}";
        $commandFilePaths = glob($commandDir . '/*.php');

        $commandClasses = [];
        foreach ($commandFilePaths as $path) {
            $commandBasename = basename($path, '.php');
            $commandClassName = "{$this->consoleConfig->getCommandNamespace()}\\$commandBasename";
            if (class_exists($commandClassName)) {
                $reflection = new \ReflectionClass($commandClassName);
                if ($reflection->isSubclassOf(Command::class)) {
                    $commandClasses[] = $commandClassName;
                } else {
                    error_log("Class '$commandClassName' should be extended from " . Command::class);
                }
            }
        }

        return $commandClasses;
    }
}
