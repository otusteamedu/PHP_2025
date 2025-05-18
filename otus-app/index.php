<?php

echo "Проверка подключений" . "<br>";
echo "mysql: ";

try {
    $conn = new mysqli(getenv('MYSQL_HOST'), getenv('MYSQL_USER'), getenv('MYSQL_PASSWORD'));
    $message = $conn->connect_error ? "Не удалось подключиться" : "Успешно подключено";

    echo $message  . "<br>";
} catch (Throwable $e) {
    echo "Ошибка подключения: " . $e->getMessage()  . "<br>";
}

echo "memcached: ";
try {
    $memcached = new Memcached();
    $memcached->addServer(getenv('MEMCACHED_HOST'), getenv('MEMCACHED_PORT'));
    $memcached->set("test_memcached_key", "Успешно подключено");

    echo $memcached->get("test_memcached_key") ?? "Не удалось подключиться";
    echo "<br>";
} catch (Throwable $e) {
    echo "Ошибка подключения: " . $e->getMessage() . "<br>";
}

echo "redis: ";
try {
    $redis = new Redis();

    $redis->connect(getenv('REDIS_HOST'), getenv('REDIS_PORT'));
    $redis->set("test_redis_key", "Успешно подключено");

    echo $redis->get("test_redis_key") ?? "Не удалось подключиться";
    echo "<br>";
} catch (Throwable $e) {
    echo "Ошибка подключения: " . $e->getMessage() . "<br>";
}
