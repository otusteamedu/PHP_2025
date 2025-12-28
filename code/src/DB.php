<?php

namespace Ak\Hw;

use PDO;

class DB
{
    private static ?PDO $instance = null;

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            self::$instance = new PDO(
                'pgsql:host=' . getenv('POSTGRES_HOST') .
                ';port=' . getenv('POSTGRES_PORT') .
                ';dbname=' . getenv('POSTGRES_DB') .
                ';user=' . getenv('POSTGRES_USER') .
                ';password=' . getenv('POSTGRES_PASSWORD')
            );
        }

        return self::$instance;
    }
}