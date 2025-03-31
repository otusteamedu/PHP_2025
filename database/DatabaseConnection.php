<?php

namespace Database;

use Exception;
use PDO;
use Dotenv\Dotenv;
use PDOException;

class DatabaseConnection
{
    private static ?self $instance = null;
    private PDO $connection;

    private function __construct()
    {
        try {
            $dotenv = Dotenv::createImmutable(__DIR__ . '../../');
            $env = $dotenv->load();

            $dsn = "mysql:host={$env['DB_HOST']};dbname={$env['DB_DATABASE']};charset=utf8mb4";

            $this->connection = new PDO(
                $dsn,
                $env['DB_USERNAME'],
                $env['DB_PASSWORD'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::ATTR_STRINGIFY_FETCHES => false
                ]
            );
        } catch (PDOException $e) {
            throw new \RuntimeException("Database connection error: " . $e->getMessage());
        }
    }

    private function __clone() {}

    /**
     * @throws Exception
     */
    public function __wakeup()
    {
        throw new Exception("Cannot unserialize singleton");
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getPDO(): PDO
    {
        return $this->connection;
    }
}