<?php
$redis = new Redis();
// подключаемся к серверу redis
$redis->connect(
  $_ENV['REDIS_APP'],
  $_ENV['REDIS_PORT']
);
// авторизуемся. 'eustatos' - пароль, который мы задали в файле `.env`
$redis->auth($_ENV['REDIS_PASSWORD']);
// публикуем сообщение в канале 'eustatos'
$redis->publish(
  'channel1',
  json_encode([
    'test' => 'success'
  ])
);
// закрываем соединение
$redis->close();