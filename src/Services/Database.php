<?php
namespace App\Services;

use PDO;

class Database
{
    public function __construct(private PDO $pdo)
    {
    }

    public function pdo(): PDO
    {
        return $this->pdo;
    }
}
