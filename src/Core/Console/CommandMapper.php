<?php

declare(strict_types=1);

namespace App\Core\Console;

use App\Core\Utils\PathResolverInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;

class CommandMapper
{
    private readonly string $namespacePrefix;

    public function __construct(
        private readonly PathResolverInterface $pathResolver,
        private readonly string $commandDir,
        string $namespacePrefix,
    ) {
        $this->namespacePrefix = rtrim($namespacePrefix, '\\') . '\\';
    }

    /**
     * @return callable[] $commands
     */
    public function getCommandsMapping(): array
    {
        $commandDir = $this->pathResolver->getSrcPath() . $this->commandDir;
        $files = glob($commandDir . '/*.php');

        $commands = [];
        foreach ($files as $file) {
            $commandClassName = $this->namespacePrefix . basename($file, '.php');
            if (class_exists($commandClassName)) {
                $reflection = new \ReflectionClass($commandClassName);
                if ($reflection->isSubclassOf(Command::class)) {
                    $commandName = $this->getCommandName($reflection, $commandClassName);
                    $commands[$commandName] = static fn() => new $commandClassName();
                }
            }
        }

        return $commands;
    }

    private function getCommandName(\ReflectionClass $reflection, string $className): string
    {
        $attributes = $reflection->getAttributes(AsCommand::class);
        if (!empty($attributes)) {
            $attribute = $attributes[0];
            $asCommand = $attribute->newInstance();
            if (isset($asCommand->name)) {
                return $asCommand->name;
            }
        }

        if (method_exists($className, 'getDefaultName')) {
            $defaultName = $className::getDefaultName();
            if ($defaultName !== null) {
                return $defaultName;
            }
        }

        throw new \RuntimeException("Command class '$className' does not have name defined.");
    }
}
