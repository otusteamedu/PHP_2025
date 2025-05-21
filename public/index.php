<?php
require  __DIR__ . '/../vendor/autoload.php';

try {
    $dotenv = Dotenv\Dotenv::createImmutable('/app/');
    $dotenv->load();
    $host = $_ENV['DB_HOST'];
    $port = $_ENV['DB_PORT'];
    $dbname = $_ENV["POSTGRES_DB"];
    $username = $_ENV["POSTGRES_USER"];
    $password = $_ENV["POSTGRES_PASSWORD"];
    $connection = new PDO("pgsql:host=$host;port=$port;dbname=$dbname;user=$username;password=$password");
    $memcached = new Memcached();
    $memcached->addServer($_ENV['MEMCACHED_HOST'], 11211);
    $redis = new Redis();
    $redis->connect($_ENV['REDIS_HOST'], 6379);

    echo "Connected successfully";
} catch (\Throwable $e) {
    dump($e->getTraceAsString());
    die("Connection failed: " . $e->getMessage());
}

