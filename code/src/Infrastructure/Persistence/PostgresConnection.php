<?php

declare(strict_types=1);

namespace MkdBot\Infrastructure\Persistence;

use MkdBot\Domain\Interface\DatabaseConnectionInterface;
use PDO;
use PDOException;

/**
 * Подключение к Postgres — реализация DatabaseConnectionInterface через PDO
 */
class PostgresConnection implements DatabaseConnectionInterface
{
    private ?PDO $pdo = null;

    public function __construct(
        private readonly string $host,
        private readonly int $port,
        private readonly string $database,
        private readonly string $user,
        private readonly string $password,
    ) {
    }

    public function getConnection(): PDO
    {
        if ($this->pdo === null) {
            $this->pdo = $this->createPdo();
        }

        return $this->pdo;
    }

    /**
     * Переподключение к БД — пересоздаёт PDO instance.
     * Вызывается при потере соединения (SQLSTATE 08006/57P01 и др.)
     */
    public function reconnect(): PDO
    {
        $this->pdo = null;
        return $this->getConnection();
    }

    /**
     * Проверяет, живо ли соединение, и при необходимости переподключается.
     * Используется в долгоживущих consumer-процессах перед каждой операцией с БД.
     */
    public function ensureConnection(): PDO
    {
        try {
            if ($this->pdo === null) {
                return $this->getConnection();
            }
            $this->pdo->query('SELECT 1');
            return $this->pdo;
        } catch (PDOException $e) {
            // SQLSTATE обрыва соединения: 08006, 57P01, 57P02, 57P03, 08003
            $sqlState = $e->errorInfo[0] ?? '';
            $reconnectCodes = ['08006', '57P01', '57P02', '57P03', '08003'];

            if (in_array($sqlState, $reconnectCodes, true)) {
                return $this->reconnect();
            }

            throw $e;
        }
    }

    private function createPdo(): PDO
    {
        $dsn = "pgsql:host={$this->host};port={$this->port};dbname={$this->database}";
        return new PDO($dsn, $this->user, $this->password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }

    public function isAvailable(): bool
    {
        try {
            $pdo = $this->getConnection();
            $pdo->query('SELECT 1');
            return true;
        } catch (PDOException) {
            return false;
        }
    }
}
