<?php

declare(strict_types=1);

namespace App\Application;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\CommandLoader\FactoryCommandLoader;

class Console
{
    private readonly Application $app;

    public function __construct()
    {
        $this->app = new Application();
    }

    public function run(): void
    {
        $commands = $this->getCommandsMapping();
        $loader = new FactoryCommandLoader($commands);
        $this->app->setCommandLoader($loader);

        $this->app->run();
    }

    /**
     * @return callable[] $commands
     */
    private function getCommandsMapping(): array
    {
        $commandDir = realpath(__DIR__ . '/../Controller/Command');
        $files = glob($commandDir . '/*.php');

        $commands = [];
        foreach ($files as $file) {
            $commandClassName = 'App\\Controller\\Command\\' . basename($file, '.php');
            if (class_exists($commandClassName)) {
                $reflection = new \ReflectionClass($commandClassName);
                if ($reflection->isSubclassOf(Command::class)) {
                    $commandClass = new $commandClassName();
                    $commands[$commandClass->getName()] = static fn() => $commandClass;
                }
            }
        }

        return $commands;
    }
}
