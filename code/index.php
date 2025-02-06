<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Домашнее задание 1</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Домашнее задание 1</h1>
    <p>Проверка загрузки css и картинок</p>
    <img src="img/2832713.jpg" alt="">
    <br>
    <br>
</body>
</html>


<?php

echo "Проверка выполнения PHP ".date("Y-m-d H:i:s")."<br>";

$redis = new Redis();
try {
    $redis->connect('redis', 6379);
    $redis->set('message', 'Привет из Redis!');
    $message = $redis->get('message');
    echo "Соединение с Redis установлено: $message";
} catch (Exception $e) {
    echo "Ошибка подключения к Redis: " . $e->getMessage();
}

echo "<br>";

$memcached = new Memcached();
$memcached->addServer('memcached', 11211);
if ($memcached->getVersion()) {
    $memcached->set('memcached_message', 'Привет из Memcached!');
    echo "Соединение с Memcached установлено: " . $memcached->get('memcached_message');
} else {
    echo "Ошибка подключения к Memcached";
}

echo "<br>";

$host = getenv('DB_HOST');
$db   = getenv('DB_DATABASE');
$user = getenv('DB_USERNAME');
$pass = getenv('DB_PASSWORD');
$charset = 'utf8mb4';
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    echo "Соединение с MySQL установлено!";
} catch (\PDOException $e) {
    echo "Ошибка подключения к MySQL: " . $e->getMessage();
}
