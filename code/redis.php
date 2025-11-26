<?php
$redis = new Redis();
$redis->connect('redis', 6379); // имя контейнера redis из docker-compose

$containerIdFromRedis = $redis->get('container_id');
if (!$containerIdFromRedis) {
    $redis->set('container_id', $_SERVER['HOSTNAME']);
    $containerIdFromRedis = $redis->get('container_id');
}


