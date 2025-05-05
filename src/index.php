<?php
// Подключение к MySQL
try {
    $pdo = new PDO("mysql:host=db;dbname=app", "user", "secret");
    echo "Connected to MySQL successfully!<br>";
} catch (PDOException $e) {
    echo "MySQL Connection failed: " . $e->getMessage() . "<br>";
}

// Подключение к Redis
$redis = new Redis();
try {
    $redis->connect('redis', 6379);
    $redis->set('test', 'Hello from Redis!');
    echo "Redis: " . $redis->get('test') . "<br>";
} catch (Exception $e) {
    echo "Redis Connection failed: " . $e->getMessage() . "<br>";
}

// Подключение к Memcached
$memcached = new Memcached();
$memcached->addServer('memcached', 11211);
$memcached->set('test', 'Hello from Memcached!');
echo "Memcached: " . $memcached->get('test') . "<br>";

// Проверка Composer
echo "Composer version: " . exec('composer --version') . "<br>";

echo "Hello from PHP-FPM!";
