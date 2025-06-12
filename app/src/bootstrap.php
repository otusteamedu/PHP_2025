<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

function getConnection(): PDO
{
    static $pdo;

    if ($pdo === null) {
        $host = 'db';
        $port = $_ENV['POSTGRES_PORT'] ?? '5432';
        $dbname = $_ENV['POSTGRES_DB_NAME'];
        $user = $_ENV['POSTGRES_USER'];
        $pass = $_ENV['POSTGRES_PASSWORD'];

        $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";

        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }

    return $pdo;
}

return getConnection();
