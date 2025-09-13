<?php

namespace App;

use PDO;
use SensitiveParameter;

readonly class Database
{
    private PDO $pdo;


    public function __construct(
        #[SensitiveParameter]
        string $host,
        #[SensitiveParameter]
        string $username,
        #[SensitiveParameter]
        string $password,
        string $charset = 'utf8mb4',
    ) {
        $dsn = sprintf(
            "mysql:host=%s;charset=%s",
            $host,
            $charset,
        );

        $this->pdo = new PDO(
            $dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_PERSISTENT => true,
            ]
        );
    }

    public function query(string $sql, array $params = []): array
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function execute(string $sql, array $params = []): int
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    public function getPdo(): PDO
    {
        return $this->pdo;
    }
}