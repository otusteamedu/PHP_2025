<?php

// PostgreSQL
$pdo = new PDO('pgsql:host=postgres;port=5432;dbname=otus_php_hw01', 'student', 'secret');
$pdo->exec('CREATE TABLE IF NOT EXISTS test (id SERIAL PRIMARY KEY, value TEXT)');
$pdo->exec("INSERT INTO test (value) VALUES ('Hello from PostgreSQL')");
$stmt = $pdo->query('SELECT * FROM test');
echo 'PostgreSQL: ' . $stmt->fetchColumn(1) . '<br>';

// Redis
$redis = new Redis();
$redis->connect('redis');
$redis->set('key', 'value');
echo 'Redis: ' . $redis->get('key') . '<br>';

// Memcached
$memcached = new Memcached();
$memcached->addServer('memcached', 11211);
$memcached->set('key', 'value');
echo 'Memcached: ' . $memcached->get('key');
