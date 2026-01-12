<?php

require 'vendor/autoload.php';

use App\Interfaces;
use App\Repository\RedisEventRepository;
use App\Services\EventMatcher;

$repository = new RedisEventRepository();
$matcher = new EventMatcher($repository);

$repository->clearAll();

$repository->addEvent([
    'priority' => 1000,
    'conditions' => ['param1' => 1],
    'event' => 'не особо важное событие'
]);

$repository->addEvent([
    'priority' => 3000,
    'conditions' => ['param1' => 1, 'param2' => 2],
    'event' => 'Важное событие'
]);

$userRequest = ['param1' => 1, 'param2' => 2];
$bestMatch = $matcher->findBestEvent($userRequest);

print_r($bestMatch);