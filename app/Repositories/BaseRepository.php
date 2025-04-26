<?php

namespace App\Repositories;

use App\Core\DatabaseConnection;
use PDO;

abstract class BaseRepository
{
    protected PDO $pdo;

    public function __construct()
    {
        $this->pdo = DatabaseConnection::getConnection();
    }
}