<?php

declare(strict_types=1);

namespace App\Core\Config;

use App\Core\Utils\PathResolver;
use Dotenv\Dotenv;

class DotEnvLoader
{
    private readonly array $params;

    public function __construct()
    {
        $rootPath = PathResolver::getRoot();
        $this->params = Dotenv::createMutable($rootPath)->load();
    }

    public function getEnv(string $var): ?string
    {
        return $this->params[$var] ?? null;
    }
}
