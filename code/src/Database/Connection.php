<?php

namespace App\Database;

use PDO;

/**
 * Singleton для хранения единственного PDO-подключения к базе данных.
 */
class Connection
{
    private static ?self $instance = null;

    private PDO $connection;

    private function __construct()
    {
        $dsn = 'pgsql:host=' . getenv('POSTGRES_HOST')
            . ';port=' . getenv('POSTGRES_PORT')
            . ';dbname=' . getenv('POSTGRES_DB');

        $this->connection = new PDO(
            $dsn,
            getenv('POSTGRES_USER'),
            getenv('POSTGRES_PASSWORD'),
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }
}
