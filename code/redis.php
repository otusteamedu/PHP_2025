<?php
$redis = new Redis();
$redis->connect('redis', 6379); // имя контейнера redis из docker-compose
$redis->set('test', 'Redis is enabled!');
echo $redis->get('test');?>


