<?php

echo '<h1>PHP Docker Environment</h1>';
echo '<p>PHP version: '.phpversion().'</p>';

// Проверка соединения с Redis
echo '<h2>Redis Connection Test</h2>';
if (class_exists('Redis')) {
    $redis = new Redis;
    try {
        $redis->connect('redis', 6379);
        $ping = $redis->ping();

        if ($ping === '+PONG' || $ping === true) {
            echo "<p style='color:green;'>Успешное подключение к Redis и получен ответ PONG!</p>";

            $key = 'test_key';
            $value = 'Привет от PHP!';
            $redis->set($key, $value);
            $retrievedValue = $redis->get($key);

            echo "<p>Установлено значение '".htmlspecialchars($value)."' для ключа '".htmlspecialchars($key)."'</p>";
            echo "<p>Получено значение для ключа '".htmlspecialchars($key)."': ".htmlspecialchars($retrievedValue).'</p>';
        } else {
            echo "<p style='color:red;'>Подключено к Redis, но команда PING не удалась. Ответ: ".htmlspecialchars($ping).'</p>';
        }
    } catch (Exception $e) {
        echo "<p style='color:red;'>Не удалось подключиться к Redis: ".htmlspecialchars($e->getMessage()).'</p>';
    }
} else {
    echo "<p style='color:red;'>Расширение Redis не установлено или не включено.</p>";
}

// Проверка соединения с Memcached
echo '<h2>Memcached Connection Test</h2>';
if (class_exists('Memcached')) {
    $memcached = new Memcached;
    $memcached->addServer('memcached', 11211);

    $key = 'memcached_test';
    $value = 'Данные из Memcached';

    if ($memcached->set($key, $value)) {
        $retrieved = $memcached->get($key);
        echo "<p style='color:green;'>Успешное подключение к Memcached!</p>";
        echo "<p>Установлено значение '".htmlspecialchars($value)."' для ключа '".htmlspecialchars($key)."'</p>";
        echo "<p>Получено значение для ключа '".htmlspecialchars($key)."': ".htmlspecialchars($retrieved).'</p>';
    } else {
        echo "<p style='color:red;'>Не удалось установить значение в Memcached.</p>";
    }
} else {
    echo "<p style='color:red;'>Расширение Memcached не установлено или не включено.</p>";
}

// Проверка соединения с PostgreSQL
echo '<h2>PostgreSQL Connection Test</h2>';
try {
    $dsn = 'pgsql:host=postgres;port=5432;dbname=myapp_db';
    $pdo = new PDO($dsn, 'myapp_user', 'myapp_pass');

    echo "<p style='color:green;'>Успешное подключение к PostgreSQL!</p>";

    $stmt = $pdo->query('SELECT version()');
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo '<p>PostgreSQL version: '.htmlspecialchars($row['version']).'</p>';
} catch (PDOException $e) {
    echo "<p style='color:red;'>Ошибка подключения к PostgreSQL: ".htmlspecialchars($e->getMessage()).'</p>';
}
