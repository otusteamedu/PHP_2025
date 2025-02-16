<?php

echo "Привет, Otus!<br>".date("Y-m-d H:i:s")."<br><br>";

echo "Запрос обработал контейнер: " . $_SERVER['HOSTNAME'];

echo "<br><br>";
$host = getenv('MYSQL_HOST');
$dbname = getenv('MYSQL_DATABASE');
$user = getenv('MYSQL_USER');
$password = getenv('MYSQL_PASSWORD');

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    echo "Подключение к MySQL успешно!";
} catch (PDOException $e) {
    echo "Ошибка подключения к MySQL: " . $e->getMessage();
}

echo "<br><br>";

try {
    $redis = new Redis();
    $redis->connect(getenv('REDIS_HOST'), getenv('REDIS_PORT'));
    echo "Connection to Redis is successful!\n";

    $redis->set('otus_key', 'Hello, Redis!');
    echo "Stored value: " . $redis->get('otus_key') . "\n";
} catch (Throwable $throwable) {
    echo 'Something went wrong while try to connect to redis: ' . $throwable->getMessage();
}

echo "<br><br>";

try {
    $memcached = new Memcached();
    $memcached->addServer(getenv('MEMCACHE_HOST'), getenv('MEMCACHE_PORT'));

    echo "Connection to Memcached is successful!\n";

    $memcached->set('test_key', 'Hello, Memcached!');
    echo "Stored value: " . $memcached->get('test_key') . "\n";
} catch (Throwable $throwable) {
    echo 'Something went wrong while try to connect to memcached: ' . $throwable->getMessage();
}
