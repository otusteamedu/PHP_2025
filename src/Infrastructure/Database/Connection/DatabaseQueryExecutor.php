<?php

namespace App\Infrastructure\Database\Connection;

class DatabaseQueryExecutor
{
    private readonly \PDO $dbh;

    public function __construct()
    {
        $this->dbh = PDOWrapper::getHandler();
        $this->dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    public function queryRows(string $sql, array $params = []): array
    {
        $sth = $this->dbh->prepare($sql);
        $sth->execute($params);

        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function queryRow(string $sql, array $params = []): array
    {
        $rows = $this->queryRows($sql, $params);

        return !empty($rows) ? $rows[0] : [];
    }

    public function execute(string $sql, array $params = []): bool
    {
        $sth = $this->dbh->prepare($sql);

        return $sth->execute($params);
    }

    public function getLastInsertId(string $sequenceName): int
    {
        return (int) $this->dbh->lastInsertId($sequenceName);
    }
}
