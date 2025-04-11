<?php

namespace App;

use \PDO;

class DB
{
    public static function getPdo(): PDO
    {
        $dsn = 'mysql:dbname=testdb;host=127.0.0.1';
        $user = 'dbuser';
        $password = 'dbpass';

        return new PDO($dsn, $user, $password);
    }
}
