<?php

declare(strict_types=1);

namespace App\Config;

final class DatabaseConfig
{
    public function __construct(
        public readonly string $host = '127.0.0.1',
        public readonly string $dbname = 'skyd_db',
        public readonly string $username = 'root',
        public readonly string $password = '',
        public readonly string $charset = 'utf8mb4'
    ) {}

    public function getDsn(): string
    {
        return "mysql:host={$this->host};dbname={$this->dbname};charset={$this->charset}";
    }
}