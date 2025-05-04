<?php

$results = [];

// Проверка Redis
try {
    $redis = new Redis();
    $connResult = $redis->connect(getenv('REDIS_HOST'), getenv('REDIS_PORT'));

    if ($connResult) {
        $redis->set('test', 'Redis работает!');
        $results[] = $redis->get('test') ?? 'Redis НЕ работает!';
    } else {
        $results[] = 'Redis НЕ работает!';
    }

} catch(Throwable $e) {
    $results[] = 'Redis: Ошибка: ' . $e->getMessage();
}


// Проверка Memcached
$memcached = new Memcached();
$memcached->addServer(getenv('MEMCACHED_HOST'), getenv('MEMCACHED_PORT'));
$memcached->set('test', 'Memcached работает!');
$results[] = $memcached->get('test') ?: 'Memcached НЕ работает!';

// Проверка Postgres
try {
    $dsn = sprintf(
        'pgsql:host=%s;port=%s;user=%s;password=%s;dbname=%s',
        getenv('DB_HOST'),
        getenv('DB_PORT'),
        getenv('DB_USERNAME'),
        getenv('DB_PASSWORD'),
        getenv('DB_DATABASE'),
    );

    $pdo = new PDO(
        $dsn,
        options: [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]
    );
    $results[] = 'Postgres: работает!';
} catch(PDOException $e) {
    $results[] = 'Postgres: Ошибка: ' . $e->getMessage();
}

echo implode('<br>', $results);
