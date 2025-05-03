<?php

use App\Food\Infrastructure\Subscriber\FoodCookingStatusSubscriber;
use App\Shared\Domain\Publisher\Publisher;

$subscribers = [
    new FoodCookingStatusSubscriber(),
];
$publisher = new Publisher();
foreach ($subscribers as $subscriber) {
    $publisher->subscribe($subscriber);
}

return $publisher;