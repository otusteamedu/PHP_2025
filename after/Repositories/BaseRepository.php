<?php

namespace Repositories;

use PDO;

abstract class BaseRepository
{
    protected PDO $pdo;

    public function __construct()
    {
        $this->pdo = new PDO(
            'mysql:host=127.0.0.1;dbname=cloud_storage;charset=utf8',
            'root',
            '');
    }
}