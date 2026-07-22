<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Connection;

use App\Infrastructure\Database\Config\DatabaseConfig;
use App\Infrastructure\Database\Exception\ConnectionException;

final class DataSourceNameFactory
{
    public static function build(DatabaseConfig $config): string
    {
        return match ($config->driver) {
            'pgsql' => sprintf(
                'pgsql:host=%s;port=%d;dbname=%s',
                $config->host,
                $config->port,
                $config->database
            ),

            default => throw new ConnectionException(
                sprintf('Unsupported driver: %s', $config->driver)
            ),
        };
    }
}