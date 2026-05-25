<?php

declare(strict_types=1);

namespace App\Core\Config;

use App\Core\Utils\PathResolverInterface;
use Dotenv\Dotenv;

class DotEnvConfig implements ConfigInterface
{
    private array $data;

    public function __construct(
        private readonly PathResolverInterface $pathResolver,
    ) {
        $projectRoot = $this->pathResolver->getProjectRoot();
        $this->data = Dotenv::createMutable($projectRoot)->load();
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->data[$key] ?? $default;
    }

    public function has(string $key): bool
    {
        return isset($this->data[$key]);
    }
}
