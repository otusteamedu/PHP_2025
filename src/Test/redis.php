<?php

$redis = new Redis();
$redis->connect(getenv('REDIS_CONTAINER'), 6379);

// Запись в Redis
$redis->set('redis-test', 'Redis test');

// Чтение из Redis
$redisValue = $redis->get('redis-test');


echo "Redis Value: " . $redisValue . "\n";
