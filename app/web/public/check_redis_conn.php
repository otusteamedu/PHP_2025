<?php

echo "Запрос обработал контейнер: " . $_SERVER["HOSTNAME"] . PHP_EOL;

$redis = new Redis();
try {
  $redis->connect('redis', 6379);
  $redis->auth($_ENV['REDIS_PASSWORD']);
  $redis->publish('hello', 'Hello World!');
  $redis->close();
} catch (Exception $e) {
  echo $e->getMessage();
}
