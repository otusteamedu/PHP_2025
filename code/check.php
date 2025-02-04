<?php

$redisPort = \getenv('REDIS_PORT');

$memcachedPort = \getenv('MEMCACHED_PORT');

$pswd = \getenv('POSTGRES_PASSWORD');
$user = \getenv('POSTGRES_USER');
$db = \getenv('POSTGRES_DB');
$port = \getenv('POSTGRES_PORT');
$host = \getenv('POSTGRES_CONTAINER_NAME');
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

$messages = ['Домашнее задание 1'];

try {
    $dsn = 'pgsql:host=' . $host . ';port=' . $port . ';dbname=' . $db;
    $pdo = new PDO($dsn, $user, $pswd, $options);

    $messages[] = 'Подключение к базе данных успешно';
} catch (Exception $e) {
    $messages[] = 'База данных: ' . $e->getMessage();
}

try {
    $redis = new Redis();
    $redis->connect('redis', $redisPort);
    $messages[] = 'Redis работает!';
} catch (RedisException $e) {
    $messages[] = 'Redis: ' . $e->getMessage();
}

$memcached = new Memcached();
$memcached->addServer('memcached', $memcachedPort);
$memcached->set('key', 'value', 10);
$value = $memcached->get('key');

if ($value) {
    $messages[] = 'Memcached работает!';
} else {
    $messages[] = 'Ошибка: ' . $memcached->getResultMessage();
}

echo \implode("<br>", $messages);
