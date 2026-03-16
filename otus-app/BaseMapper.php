<?php

namespace App;

use PDO;

class BaseMapper
{
    private PDO $pdo;

    public function __construct(
    ){
        $this->pdo = new PDO(
            getenv('MYSQL_URL'),
            getenv('MYSQL_USER'),
            getenv('MYSQL_PASSWORD'),
        );
    }

    public function getPdo(): PDO
    {
        return $this->pdo;
    }
}