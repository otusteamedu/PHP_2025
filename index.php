<?php

require 'vendor/autoload.php';

use Predis\Client;
use App\EventSystem\EventSystem;
use App\Redis\RedisStorage;

$redis = new Client();

$redisStorage = new RedisStorage($redis);

$eventSystem = new EventSystem($redisStorage);

// Добавляем события
$eventSystem->addEvent(1000, ['param1' => 1], '::event1::');
$eventSystem->addEvent(2000, ['param1' => 2, 'param2' => 2], '::event2::');
$eventSystem->addEvent(3000, ['param1' => 1, 'param2' => 2], '::event3::');

$request = ['param1' => 1, 'param2' => 2];

// Находим наиболее подходящее событие
$bestEventMessage = $eventSystem->getBestMatchingEvent($request);
echo $bestEventMessage;

// Очищаем все события
$eventSystem->clearAllEvents();
