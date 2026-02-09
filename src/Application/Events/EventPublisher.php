<?php

namespace Restaurant\Application\Events;

use Restaurant\Domain\Interfaces\EventPublisherInterface;

readonly class EventPublisher implements EventPublisherInterface
{
    private readonly EventManager $eventManager;

    public function __construct(EventManager $eventManager)
    {
        $this->eventManager = $eventManager;
    }

    public function publishOrderEvent(int $orderId, string $status, string $timestamp): void
    {
        $this->eventManager->notifyOrderEvent($orderId, $status, $timestamp);
    }
}
