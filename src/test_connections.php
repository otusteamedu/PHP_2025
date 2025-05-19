<?php

use App\MemcachedDriver;
use App\PgSQLDriver;
use App\RedisDriver;

$connections = [];

try {
    $pgSql = new PgSQLDriver();
    $connections['Postgres'] = $pgSql->getVersion();
} catch (PDOException $e) {
    $connections['Postgres'] = $e->getMessage();
}

try {
    $redis = new RedisDriver();
    $connections['Redis'] = $redis->getVersion();
} catch (RuntimeException $e) {
    $connections['Redis'] = $e->getMessage();
}

try {
    $memcached = new MemcachedDriver();
    $connections['Memcached'] = $memcached->getVersion();
} catch (RuntimeException $e) {
    $connections['Memcached'] = $e->getMessage();
}

foreach ($connections as $connection => $msg) {
    echo "$connection: $msg<br>";
}
