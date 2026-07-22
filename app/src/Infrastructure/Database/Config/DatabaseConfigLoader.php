<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Config;

final class DatabaseConfigLoader
{
    public static function load(): DatabaseConfig
    {
        $isTesting = getenv('APP_ENV') === 'testing';

        return new DatabaseConfig(
            getenv('DB_DRIVER'),
            $isTesting ? getenv('DB_TEST_HOST') : getenv('DB_HOST'),
            (int) ($isTesting ? getenv('DB_TEST_PORT') : getenv('DB_PORT')),
            $isTesting ? getenv('DB_TEST_NAME') : getenv('DB_NAME'),
            $isTesting ? getenv('DB_TEST_USER') : getenv('DB_USER'),
            $isTesting ? getenv('DB_TEST_PASSWORD') : getenv('DB_PASSWORD'),
        );
    }
}