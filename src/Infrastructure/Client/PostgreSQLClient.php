<?php
declare(strict_types=1);

namespace Dinargab\Homework20\Infrastructure\Client;

use PDO;

class PostgreSQLClient
{
    private PDO $connection;
    public function __construct()
    {
        $host = getenv('DB_HOST');
        $port = getenv('DB_PORT');
        $dbname = getenv('DB_DATABASE');
        $user = getenv('DB_USERNAME');
        $password = getenv('DB_PASSWORD');

        $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
        $this->connection = new PDO($dsn, $user, $password);

        // Set PDO error mode to exception for better error handling
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }
}