<?php

declare(strict_types=1);

namespace App\Core\Database\Connection;

use App\Core\Database\Exception\ConfigurationException;
use App\Core\Database\Exception\DatabaseException;

class DatabaseQueryExecutor
{
    private const string CONNECTION_SQLSTATE_CLASS = '08';
    private const array CONNECTION_FAILURE_MARKERS = [
        'no connection to the server',
        'server closed the connection unexpectedly',
        'terminating connection',
        'connection refused',
        'broken pipe',
    ];

    public function __construct(
        private readonly PDOWrapper $pdoWrapper,
    ) {
    }

    /**
     * @throws ConfigurationException
     * @throws DatabaseException
     */
    public function queryRows(string $sql, array $params = []): array
    {
        return $this->runQuery($sql, $params)->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * @throws ConfigurationException
     * @throws DatabaseException
     */
    public function queryRow(string $sql, array $params = []): array
    {
        $row = $this->runQuery($sql, $params)->fetch(\PDO::FETCH_ASSOC);

        return $row !== false ? $row : [];
    }

    /**
     * @throws ConfigurationException
     * @throws DatabaseException
     */
    public function execute(string $sql, array $params = []): void
    {
        $this->runQuery($sql, $params);
    }

    /**
     * @throws ConfigurationException
     * @throws DatabaseException
     */
    public function getLastInsertId(string $sequenceName): int
    {
        try {
            return (int) $this->pdo()->lastInsertId($sequenceName);
        } catch (\PDOException $e) {
            throw $this->wrap($e);
        }
    }

    /**
     * @throws ConfigurationException
     * @throws DatabaseException
     */
    private function runQuery(string $sql, array $params = []): \PDOStatement
    {
        try {
            $sth = $this->pdo()->prepare($sql);
            $sth->execute($params);

            return $sth;
        } catch (\PDOException $e) {
            throw $this->wrap($e);
        }
    }

    /**
     * @throws ConfigurationException
     * @throws DatabaseException
     */
    private function pdo(): \PDO
    {
        return $this->pdoWrapper->getHandler();
    }

    private function wrap(\PDOException $e): DatabaseException
    {
        if ($this->isConnectionFailure($e)) {
            $this->pdoWrapper->reset();
        }

        return new DatabaseException(
            sprintf('Database operation failed: %s', $e->getMessage()),
            previous: $e,
        );
    }

    private function isConnectionFailure(\PDOException $e): bool
    {
        $code = $e->getCode();
        if (is_string($code) && str_starts_with($code, self::CONNECTION_SQLSTATE_CLASS)) {
            return true;
        }

        $message = strtolower($e->getMessage());

        return array_any(
            self::CONNECTION_FAILURE_MARKERS,
            static fn(string $marker) => str_contains($message, $marker),
        );
    }
}
