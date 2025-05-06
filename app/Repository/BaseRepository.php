<?php

declare(strict_types=1);

namespace App\Repository;

use App\Database\DatabaseConnection;
use PDO;

abstract class BaseRepository
{
    protected PDO $pdo;

    public function __construct()
    {
        $this->pdo = DatabaseConnection::getConnection();
    }
}