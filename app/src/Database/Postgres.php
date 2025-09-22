<?php
declare(strict_types = 1);

namespace App\Database;

use PDO;
use PDOStatement;
use RuntimeException;

final class Postgres implements Database
{
    private ?PDO $connection = null;
    private ?PDOStatement $statement = null;

    public function __construct(private readonly Config $config) {}

    private function connect(): void
    {
        if ($this->connection) {
            return;
        }

        $dsn = sprintf(
            "pgsql:host=%s;dbname=%s;port=%d",
            $this->config->hostname,
            $this->config->database,
            $this->config->port
        );

        if ($this->config->ssl) {
            $dsn .= ";sslmode=require";
        }

        $this->connection = new PDO($dsn, $this->config->username, $this->config->password);
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $this->connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

    }

    public function begin(): bool
    {
        $this->connect();
        return $this->connection->beginTransaction();
    }

    public function commit(): bool
    {
        $this->connect();
        return $this->connection->commit();
    }

    public function rollback(): bool
    {
        $this->connect();
        return $this->connection->rollBack();
    }

    public function prepare(string $sqlText): void
    {
        $this->connect();
        $this->statement = $this->connection->prepare($sqlText);
    }

    public function query(string $sqlText, ?array $bindings = []): bool
    {
        $this->prepare($sqlText);

        return $this->execute($bindings);
    }

    private function execute(?array $bindings = []): bool
    {
        if ($this->statement === null) {
            throw new RuntimeException('Statement is not prepared');
        }

        if (!$this->statement->execute($bindings)) {
            $error = [
                'query' => $this->statement->queryString,
                'code' => $this->statement->errorCode(),
                'error' => $this->statement->errorInfo(),
            ];

            throw new RuntimeException(json_encode($error, JSON_THROW_ON_ERROR));
        }

        return true;
    }

    public function fetchAll(string $sqlText, ?array $bindings = []): array
    {
        $this->query($sqlText, $bindings);
        try {
            return $this->statement->fetchAll() ?? [];
        } finally {
            $this->statement->closeCursor();
        }
    }

    public function fetch(string $sqlText, ?array $bindings = []): ?array
    {
        $this->query($sqlText, $bindings);
        try {
            $row = $this->statement->fetch();
            if ($row === false) {
                return null;
            }
            return $row;
        } finally {
            $this->statement->closeCursor();
        }
    }

    public function fetchValue(string $sqlText, ?array $bindings = []): ?string
    {
        $this->query($sqlText, $bindings);
        try {
            if ($this->statement === null) {
                return null;
            }
            $value = $this->statement->fetchColumn(0);
            if ($value === false || $value === null) {
                return null;
            }
            return is_scalar($value) ? (string) $value : null;
        } finally {
            $this->statement->closeCursor();
        }
    }
}
