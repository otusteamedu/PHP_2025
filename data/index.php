<?php

echo 'HW1';
echo '<br>';

$dsn = 'mysql:host=mysql;dbname=' . getenv('MYSQL_DATABASE') . ';charset=utf8';
$user = getenv('MYSQL_USER');
$password = getenv('MYSQL_PASSWORD');

try {
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    echo 'Подключение к базе данных MYSQL успешно!';
} catch (PDOException $e) {
    echo 'MYSQL: ' . $e->getMessage();
}


echo '<br>';

try {
    $redis = new Redis();
    $redis->connect('redis', 6379);

    echo 'Подключение к Redis успешно!';
} catch (RedisException $e) {
    echo 'Redis: ' . $e->getMessage();
}


echo '<br>';

$memcached = new Memcached();
$memcached->addServer('memcached', 11211);

$memcached->set('test_key', 'test!', 10);

$value = $memcached->get('test_key');

if ($value) {
    echo 'Memcached работает! Значение: ' . $value;
} else {
    echo 'Ошибка: ' . $memcached->getResultMessage();
}
