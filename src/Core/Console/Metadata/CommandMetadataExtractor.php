<?php

declare(strict_types=1);

namespace App\Core\Console\Metadata;

use Symfony\Component\Console\Attribute\AsCommand;

class CommandMetadataExtractor
{
    public function __construct(
        private readonly array $commandClasses,
    ) {
    }

    /**
     * @return CommandMetadata[]
     */
    public function extractCommandsMetadata(): array
    {
        $commandsMetadata = [];
        foreach ($this->commandClasses as $commandClass) {
            $commandsMetadata[] = $this->extractSingleCommandMetadata($commandClass);
        }

        return $commandsMetadata;
    }

    private function extractSingleCommandMetadata(string $commandClass): CommandMetadata
    {
        $reflection = new \ReflectionClass($commandClass);
        $constructor = $reflection->getConstructor();

        $dependencyNames = [];
        if ($constructor !== null) {
            foreach ($constructor->getParameters() as $param) {
                $typeHint = $param->getType();
                if ($typeHint !== null && !$typeHint->isBuiltin()) {
                    $dependencyNames[] = $typeHint->getName();
                }
            }
        }

        $commandName = $this->getCommandName($reflection);

        return new CommandMetadata(
            className: $commandClass,
            commandName: $commandName,
            dependencyNames: $dependencyNames,
        );
    }

    private function getCommandName(\ReflectionClass $reflection): string
    {
        $attributes = $reflection->getAttributes(AsCommand::class);
        if (!empty($attributes)) {
            $attribute = $attributes[0];
            $asCommand = $attribute->newInstance();
            if (isset($asCommand->name)) {
                return $asCommand->name;
            }
        }

        $className = $reflection->getName();
        if (method_exists($className, 'getDefaultName')) {
            $defaultName = $className::getDefaultName();
            if ($defaultName !== null) {
                return $defaultName;
            }
        }

        throw new \RuntimeException("Command class '$className' does not have name defined.");
    }
}
