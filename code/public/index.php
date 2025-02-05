<?php

$redis = new Redis;
$redis->connect('redis', 6379);

if ($redis->ping()) {
    echo 'Redis подключено <br>';
}

$mysql_host = 'mysql';
$user = 'webmaster';
$pass = 'secret';
$db_name = 'app_db';

$conn = new mysqli(
    $mysql_host,
    $user,
    $pass,
    $db_name
);

if (! $conn->connect_error) {
    echo 'MySQL подключено <br>';
}

$conn->close();

$memcached = new Memcached;
$memcached->addServer('memcached', 11211);
$memcached->set('key', true);

if ($memcached->get('key')) {
    echo 'Memcached подключено <br>';
}

echo '<hr>';

phpinfo();