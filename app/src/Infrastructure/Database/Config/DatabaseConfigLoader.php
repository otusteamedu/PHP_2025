<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Config;

final class DatabaseConfigLoader
{
    public static function load(): DatabaseConfig
    {
        return new DatabaseConfig(
            getenv('DB_DRIVER'),
            getenv('DB_HOST'),
            (int)getenv('DB_PORT'),
            getenv('DB_NAME'),
            getenv('DB_USER'),
            getenv('DB_PASSWORD'),
        );
    }
}
