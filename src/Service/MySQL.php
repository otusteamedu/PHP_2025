<?php

namespace Blarkinov\PhpDbCourse\Service;

use Exception;
use PDO;

class MySQL
{
    private static ?PDO $conn = null;

    public function __construct()
    {
        if (!self::$conn) {
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
    }

    public static function getConn(): PDO
    {
        if (!self::$conn)
            new self();

        return self::$conn;
    }

    public static function execute(string $statement, array $parameters = [])
    {
        if (!self::$conn)
            self::getConn();

        $stmt = self::$conn->prepare($statement);
        $stmt->execute($parameters);
        return $stmt;
    }


    public function select(string $statement = "", $parameters = [])
    {
        $stmt = $this->execute($statement, $parameters);
        return $stmt->fetchAll();
    }

    public function delete(string $statement = "", $parameters = [])
    {
        $this->execute($statement, $parameters);
    }

    public function insert(string $statement = "", $parameters = [])
    {
        $this->execute($statement, $parameters);
        return self::$conn->lastInsertId();
    }

    public function getErrorMessage(): array
    {
        return self::$conn->errorInfo();
    }

    public function update(string $statement = "", $parameters = [])
    {
        $this->execute($statement, $parameters);
    }

    public function getPDO(): PDO
    {
        return self::$conn;
    }
}
