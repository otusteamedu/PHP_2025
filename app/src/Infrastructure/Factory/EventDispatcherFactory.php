<?php

declare(strict_types=1);

namespace App\Infrastructure\Factory;

use App\Application\EventDispatcher\EventDispatcher;
use App\Application\EventDispatcher\EventDispatcherInterface;

readonly class EventDispatcherFactory
{
    public function __invoke(iterable $listeners): EventDispatcherInterface
    {
        $eventDispatcher = new EventDispatcher();

        foreach ($listeners as $listener) {
            foreach ($listener->getSubscribedEvents() as $eventName) {
                $eventDispatcher->subscribe($eventName, $listener);
            }
        }

        return $eventDispatcher;
    }
}
