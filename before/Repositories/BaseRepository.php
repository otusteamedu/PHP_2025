<?php

namespace Repositories;

class BaseRepository
{
    public \PDO $pdo;

    public function __construct()
    {
        try {
            $this->pdo = new \PDO('mysql:host=127.0.0.1;dbname=cloud_storage;charset=utf8',
                'root', '');
        } catch (\PDOException $e) {
            echo $e->getMessage();
        }
    }
}


