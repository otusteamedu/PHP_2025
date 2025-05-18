<?php
require  __DIR__ . '/../vendor/autoload.php';

try {
    $dotenv = Dotenv\Dotenv::createImmutable('/app/');
    $dotenv->load();
    $host = "app_db";
    $port = $_ENV['DB_PORT'];
    $dbname = $_ENV["POSTGRES_DB"];
    $username = $_ENV["POSTGRES_USER"];
    $password = $_ENV["POSTGRES_PASSWORD"];
    $connection = new PDO("pgsql:host=$host;port=$port;dbname=$dbname;user=$username;password=$password");
    $memcached = new Memcached();
    $memcached->addServer('memcached', 11211);
    $redis = new Redis();
    $redis->connect('app_redis', 6379);

    echo "Connected successfully";
} catch (\Throwable $e) {
    die("Connection failed: " . $e->getMessage());
}

