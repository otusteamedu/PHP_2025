<?php

declare(strict_types=1);

namespace App\Application\EventDispatcher;

use App\Application\EventListener\EventListenerInterface;

interface EventDispatcherInterface
{
    public function subscribe(string $eventName, EventListenerInterface $listener): void;

    public function unsubscribe(string $eventName, EventListenerInterface $listener): void;

    public function dispatch(object $event, ?string $eventName = null): void;
}
