<?php
declare(strict_types=1);

namespace App\Infrastructure\Database;

use PDO;

class PdoFactory
{
    public static function create(): PDO
    {
        $dsn = sprintf(
            'pgsql:host=%s;port=%d;dbname=%s',
            $_ENV['POSTGRES_HOST'],
            (int) $_ENV['POSTGRES_PORT'],
            $_ENV['POSTGRES_DB'],
        );

        return new PDO(
            $dsn,
            $_ENV['POSTGRES_USER'],
            $_ENV['POSTGRES_PASSWORD'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
    }
}
