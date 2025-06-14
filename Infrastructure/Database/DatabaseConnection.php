<?php

declare(strict_types=1);

namespace Infrastructure\Database;

use App\Helper;
use Dotenv\Dotenv;
use PDO;

class DatabaseConnection
{
    private static ?PDO $connection = null;

    /**
     * @return PDO
     */
    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            $env = Helper::getEnv();
            $dsn = "{$env['DB_CONNECTION']}:host={$env['DB_HOST']};dbname={$env['DB_DATABASE']};charset=utf8mb4";

            self::$connection = new PDO(
                $dsn,
                $env['DB_USERNAME'],
                $env['DB_PASSWORD'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        }

        return self::$connection;
    }
}

