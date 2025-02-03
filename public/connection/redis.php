<?php

$host = getenv('REDIS_HOST');
$port = 6379;
$password = getenv('REDIS_PASSWORD');

$message = "REDIS CONNECTION - ";

try {
    $redis = new Redis();
    $redis->connect($host, $port);
    $redis->auth("$password");
    $message .= "OK!\r\n";
} catch (RedisException $e) {
    echo $e->getMessage();
    $message .= "FAIL!\r\n";
}

echo $message;
