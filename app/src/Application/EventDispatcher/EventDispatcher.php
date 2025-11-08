<?php

declare(strict_types=1);

namespace App\Application\EventDispatcher;

use App\Application\EventListener\EventListenerInterface;

class EventDispatcher implements EventDispatcherInterface
{
    private array $listeners = [];

    public function subscribe(string $eventName, EventListenerInterface $listener): void
    {
        $listenerName = get_class($listener);

        if (!key_exists($eventName, $this->listeners)) {
            $this->listeners[$eventName] = [];
        }
        $this->listeners[$eventName][$listenerName] = $listener;
    }

    public function unsubscribe(string $eventName, EventListenerInterface $listener): void
    {
        $listenerName = get_class($listener);

        if (!key_exists($eventName, $this->listeners)) {
            return;
        }

        if (key_exists($listenerName, $this->listeners[$eventName])) {
            unset($this->listeners[$eventName][$listenerName]);
        }
    }

    public function dispatch(object $event, ?string $eventName = null): void
    {
        $eventName = $eventName ?: get_class($event);
        if (!key_exists($eventName, $this->listeners)) {
            return;
        }

        foreach ($this->listeners[$eventName] as $listener) {
            /** ListenerInterface $listener */
            $listener->handle($event);
        }
    }
}
