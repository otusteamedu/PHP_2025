<?php

declare(strict_types=1);

namespace App\Application;

use Dotenv\Dotenv;

class DotEnvLoader
{
    private readonly array $params;

    public function __construct()
    {
        $this->params = Dotenv::createMutable(__DIR__ . '/../../')->load();
    }

    public function getEnv(string $var): ?string
    {
        return $this->params[$var] ?? null;
    }
}
