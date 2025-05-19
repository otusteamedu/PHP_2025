<?php

namespace App;

use Dotenv\Dotenv;

class DotEnvLoader
{
    private readonly array $params;

    public function __construct()
    {
        $this->params = Dotenv::createImmutable(__DIR__ . '/../')->load();
    }

    public function getEnv(string $var): ?string
    {
        return $this->params[$var] ?? null;
    }
}
