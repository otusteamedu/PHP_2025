<?php
echo "<h1>PHP-FPM + Nginx + Redis + Memcached + MySQL</h1>";

// Redis
$redis = new Redis();
$redis->connect('redis', 6379);
$redis->set('test', 'Hello from Redis!');
echo "<p>Redis: " . $redis->get('test') . "</p>";

// Memcached
$memcached = new Memcached();
$memcached->addServer('memcached', 11211);
$memcached->set('test', 'Hello from Memcached!');
echo "<p>Memcached: " . $memcached->get('test') . "</p>";

// MySQL
try {
    $pdo = new PDO('mysql:host=db;dbname=default', 'user', '123');
    echo "<p>MySQL: Connected successfully</p>";
} catch (PDOException $e) {
    echo "<p>MySQL Error: " . $e->getMessage() . "</p>";
}