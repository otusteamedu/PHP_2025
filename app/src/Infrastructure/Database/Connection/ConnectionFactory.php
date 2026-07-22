<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Connection;

use App\Infrastructure\Database\Config\DatabaseConfig;
use PDO;

final class ConnectionFactory
{
    public static function create(DatabaseConfig $config): PDO
    {
        $dsn = DataSourceNameFactory::build($config);

        return new PDO(
            $dsn,
            $config->username,
            $config->password,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
    }
}