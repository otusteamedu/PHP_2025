<?php

namespace App\Controllers;

use App\Database;
use PDOException;

class IndexController
{
    public function index(Database $database): void
    {
        try {
            $queryResult = $database->query('SELECT VERSION();');
            $dbInfo = var_export($queryResult, true);
        } catch (PDOException $e) {
            $dbInfo = $e->getMessage();
        }
        echo "hello";
    }
}