<?php

namespace Arlex2305k\PatternsDb;

use Dotenv\Dotenv;

class DatabaseConnection
{
    public static function createConnection(): \PDO
    {
        if (file_exists(__DIR__ . '/../.env')) {
            $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
            $dotenv->load();
        }
        $host = $_ENV['PG_HOST'] ?? 'localhost';
        $port = $_ENV['PG_PORT'] ?? '5432';
        $dbname = $_ENV['PG_DB'] ?? 'postgres';
        $username = $_ENV['PG_USER'] ?? 'user';
        $password = $_ENV['PG_PASSWORD'] ?? '';

        $dsn = "pgsql:host={$host};port={$port};dbname={$dbname}";
        $options = [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            return new \PDO($dsn, $username, $password, $options);
        } catch (\PDOException $e) {
            throw new \PDOException("Ошибка подключения к базе данных: " . $e->getMessage());
        }
    }
}
