<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Config;

final readonly class DatabaseConfig
{
    public function __construct(
        public string $driver,
        public string $host,
        public int $port,
        public string $database,
        public string $username,
        public string $password,
    )
    {
    }
}