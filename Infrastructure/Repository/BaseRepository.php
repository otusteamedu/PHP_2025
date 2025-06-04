<?php

declare(strict_types=1);

namespace Infrastructure\Repository;

use Infrastructure\Database\DatabaseConnection;
use PDO;

abstract class BaseRepository
{
    protected PDO $pdo;

    public function __construct()
    {
        $this->pdo = DatabaseConnection::getConnection();
    }
}

