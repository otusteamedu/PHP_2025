<?php

declare(strict_types=1);

namespace App\Core\Container\Config\Loaders;

use App\Core\Container\Config\Data\DotEnv\DotEnvConfig;
use App\Core\Container\Config\Data\DotEnv\DotEnvConfigInterface;
use App\Core\Utils\PathResolverInterface;
use Dotenv\Dotenv;

class DotEnvConfigLoader implements ConfigLoaderInterface
{
    public function __construct(
        private readonly PathResolverInterface $pathResolver,
    ) {
    }

    public function load(): DotEnvConfigInterface
    {
        $projectRoot = $this->pathResolver->getProjectRoot();
        $data = Dotenv::createMutable($projectRoot)->load();

        return new DotEnvConfig($data);
    }
}
