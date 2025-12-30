<?php

declare(strict_types=1);

namespace Otus\DataMapper\Connection;

use PDO;

final class Database
{
    /**
     * @var Database|null
     */
    private static ?Database $instance = null;

    /**
     * @var PDO
     */
    private readonly PDO $pdo;

    private function __construct()
    {
        $this->pdo = new PDO(env('DATABASE_DSN'), env('DATABASE_USERNAME'), env('DATABASE_PASSWORD'));

        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    }

    /**
     * @return Database
     */
    public static function getInstance(): self
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @return PDO
     */
    public function getPdo(): PDO
    {
        return $this->pdo;
    }
}
