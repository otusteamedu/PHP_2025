<?php

// Redis
$redis = new Redis();
$redis->connect('redis', 6379);
$redis->set('key', 'redis');
echo $redis->get('key');

echo "\n";

// Memcached
$memcached = new Memcached();
$memcached->addServer('memcached', 11211);
$memcached->set('key', 'memcached');
echo $memcached->get('key');

echo "\n";

// MySQL
$mysqli = new mysqli('mysql', 'root', 'root', 'database');

if ($mysqli->connect_error) {
    die("Ошибка подключения к MySQL: " . $mysqli->connect_error);
}

echo "mysql";

$mysqli->close();
