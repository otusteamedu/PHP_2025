<?php

namespace Blarkinov\PhpDbCourse\Service;

use Exception;
use PDO;

class MySQL
{
    private static ?PDO $conn = null;

    private function __construct()
    {
        $dsn = "mysql:host=$_ENV[MYSQL_HOST];dbname=$_ENV[MYSQL_DATABASE]";

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            self::$conn = new PDO($dsn, $_ENV['MYSQL_USER'], $_ENV['MYSQL_USER_PASSWORD'], $options);
        } catch (Exception $e) {
            throw new Exception('error connection mysql: ' . $e->getMessage());
        }
    }
    public static function getConn()
    {
        if (self::$conn)
            return self::$conn;
        else
            return new self();
    }
}
