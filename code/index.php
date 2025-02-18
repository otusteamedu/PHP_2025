<?php

echo '<h1>Otus homework 1</h1>';

echo 'Current datetime: ' . date("Y-m-d H:i:s")  . '<br><br>';
echo 'The request was processed by the container: ' . $_SERVER['HOSTNAME']  . '<br><br>';

// Testing MySQL
$dbName = getenv('MYSQL_DATABASE');
$dbUser = getenv('MYSQL_USER');
$dbPassword = getenv('MYSQL_PASSWORD');

try {
    $pdo = new PDO("mysql:host=mysql;dbname=$dbName;charset=utf8", $dbUser, $dbPassword);
    echo 'Connection to MySQL successful.';
} catch (PDOException $e) {
    echo 'Error connecting to MySQL:' . $e->getMessage();
}

echo '<br><br>';

// Testing Redis
try {
    $redis = new Redis();
    $redis->connect('redis');
    echo 'Connection to Redis successful.<br>';

    $redis->set('test_redis_key', 'Test redis value.');
    echo 'Redis value: ' . $redis->get('test_redis_key');
} catch (Throwable $e) {
    echo 'Error connecting to Redis: ' . $e->getMessage();
}

echo '<br><br>';

// Testing Memcached
try {
    $memCached = new Memcached();
    $memCached->addServer('memcached', 11211);

    echo 'Connection to Redis successful.<br>';

    $memCached->set('test_memcached_key', 'Test memcached value.');
    echo 'Memcached value:' . $memCached->get('test_memcached_key');
} catch (Throwable $e) {
    echo 'Error connecting to Redis: ' . $e->getMessage();
}
