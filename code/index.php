<?php
require_once __DIR__ . '/vendor/autoload.php';

use App\EventStorage;

$redis = new Redis();
$redis->connect('redis', 6379);

$storage = new EventStorage($redis);

$storage->addEvent(1000, ['param1' => 1], 'Event 1');
$storage->addEvent(2000, ['param1' => 2, 'param2' => 2], 'Event 2');
$storage->addEvent(3000, ['param1' => 1, 'param2' => 2], 'Event 3');

$params = ['param1' => 1, 'param2' => 2];
$bestEvent = $storage->getBestEvent($params);

echo "Наиболее подходящее событие: " . json_encode($bestEvent);