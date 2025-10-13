<?php
namespace App\Infrastructure;

use PDO;
use PDOException;

class Database
{
    private PDO $pdo;

    public function __construct(string $host, int $port, string $database, string $user, string $pass)
    {
        $dsn = "pgsql:host=$host;port=$port;dbname=$database";

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];
        $this->pdo = new PDO($dsn, $user, $pass, $options);
    }

    public function pdo(): PDO
    {
        return $this->pdo;
    }
}
