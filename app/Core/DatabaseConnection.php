<?php

// app/Core/DatabaseConnection.php
namespace App\Core;

use PDO;
use PDOException;
use Dotenv\Dotenv;

class DatabaseConnection
{
    private static ?PDO $connection = null;

    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            $dotenv = Dotenv::createArrayBacked(__DIR__.'/../../');
            $env = $dotenv->load();

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