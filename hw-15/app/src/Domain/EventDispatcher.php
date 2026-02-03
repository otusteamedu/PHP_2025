<?php

declare(strict_types=1);

namespace App\Domain;

class EventDispatcher
{
    private array $listeners = [];

    public function addListener(string $eventName, callable $listener): void
    {
        $this->listeners[$eventName][] = $listener;
    }

    public function dispatch(object $event): void
    {
        foreach ($this->listeners[get_class($event)] ?? [] as $listener) {
            $listener($event);
        }
    }
}
