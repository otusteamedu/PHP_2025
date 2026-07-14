<?php

declare(strict_types=1);

namespace App\Core\Container\Config\Loaders;

use App\Core\Container\Config\Contracts\DotEnvConfigInterface;
use App\Core\Container\Config\Types\DotEnv;
use App\Core\Utils\PathResolverInterface;
use Dotenv\Dotenv as PhpDotenv;

class DotEnvLoader implements ConfigLoaderInterface
{
    public function __construct(
        private readonly PathResolverInterface $pathResolver,
    ) {
    }

    public function load(): DotEnvConfigInterface
    {
        $projectRoot = $this->pathResolver->getProjectRoot();
        $data = PhpDotenv::createMutable($projectRoot)->load();

        return new DotEnv($data);
    }
}
