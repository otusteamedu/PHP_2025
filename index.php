<?php

require 'vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv = $dotenv->load();

$redisHost = $dotenv['REDIS_HOST'];
$redisPort = $dotenv['REDIS_PORT'];

$storage = new EventStorage($redisHost, $redisPort);

$storage->addEvent(1000, ['param1' => 1], ['event' => 'event_1']);
$storage->addEvent(2000, ['param1' => 2, 'param2' => 2], ['event' => 'event_2']);
$storage->addEvent(3000, ['param1' => 1, 'param2' => 2], ['event' => 'event_3']);

$userRequest = [
    'params' => [
        'param1' => 1,
        'param2' => 2,
    ]
];

$bestEvent = $storage->getBestEvent($userRequest['params']);
if ($bestEvent) {
    echo "Best Event: " . json_encode($bestEvent);
} else {
    echo "No suitable event found.";
}

// Очистка всех событий
//$storage->clearEvents();
