<?php

declare(strict_types=1);

namespace Otus\Queue\Infrastructure\Database;

use Iterator;
use PDO;
use PDOException;

final readonly class Database implements DatabaseInterface
{
    /**
     * @var PDO
     */
    private PDO $pdo;

    /**
     * @param string $dsn
     * @param string|null $username
     * @param string|null $password
     * @param array $options
     */
    public function __construct(
        string $dsn,
        ?string $username = null,
        ?string $password = null,
        array $options = [],
    ) {
        $this->pdo = new PDO($dsn, $username, $password, $options);
    }

    /**
     * @param string $sql
     * @param array $params
     *
     * @return bool
     */
    public function command(string $sql, array $params = []): bool
    {
        return $this
            ->pdo
            ->prepare($sql)
            ->execute($params);
    }

    /**
     * @param string $sql
     *
     * @return Iterator
     */
    public function query(string $sql): Iterator
    {
        return $this
            ->pdo
            ->query($sql)
            ->getIterator();
    }

    /**
     * @return bool
     */
    public function ping(): bool
    {
        try {
            return (bool) $this->pdo->query('SELECT 1');
        } catch (PDOException) {
            return false;
        }
    }
}
