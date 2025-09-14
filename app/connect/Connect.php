<?php

declare(strict_types=1);

namespace App\Connect;

use PDO;

class Connect
{
    private PDO $pdo;

    public function __construct()
    {
        $dsn = 'pgsql:host=db;port=5432;dbname=blog';
        $user = 'root';
        $password = 'password';

        $this->pdo = new PDO($dsn, $user, $password);

    }

    public function connect(): PDO
    {
        return $this->pdo;
    }
}
