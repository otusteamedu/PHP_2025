<?php

declare(strict_types=1);

namespace App\Core\Container\Config\Loaders;

use App\Core\Container\Config\Contracts\ConfigInterface;
use App\Core\Container\Config\Types\Console;
use App\Core\Utils\PathResolverInterface;

class ConsoleLoader implements ConfigLoaderInterface
{
    public function __construct(
        private readonly PathResolverInterface $pathResolver,
    ) {
    }

    public function load(): ConfigInterface
    {
        $config = require $this->pathResolver->getConfigPath() . '/base.php';

        $commandDir = $config['console']['command_dir'] ?? 'Controller/Cli/Command';
        $baseNamespace = $config['main']['base_namespace'] ?? 'App';

        $commandDir = trim($commandDir, '/');
        $commandNamespace = $this->computeCommandNamespace($commandDir, $baseNamespace);

        return new Console(commandDir: $commandDir, commandNamespace: $commandNamespace);
    }

    private function computeCommandNamespace(string $commandDir, string $baseNamespace): string
    {
        $namespaceParts = explode('/', $commandDir);
        array_unshift($namespaceParts, $baseNamespace);

        return implode('\\', $namespaceParts);
    }
}
