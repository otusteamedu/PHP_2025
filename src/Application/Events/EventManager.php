<?php

namespace Restaurant\Application\Events;

use Restaurant\Domain\Interfaces\ObserverInterface;
use Restaurant\Domain\Interfaces\SubjectInterface;
use Restaurant\Domain\Interfaces\EventPublisherInterface;
use Restaurant\Application\DTO\OrderEventDTO;

class EventManager implements SubjectInterface, EventPublisherInterface
{
    public function __construct(
        private array $observers = []
    ) {
    }

    public function attach(ObserverInterface $observer): void
    {
        $this->observers[] = $observer;
    }

    public function detach(ObserverInterface $observer): void
    {
        $key = array_search($observer, $this->observers, true);
        if ($key !== false) {
            unset($this->observers[$key]);
        }
    }

    public function notify(mixed $data = null): void
    {
        foreach ($this->observers as $observer) {
            $observer->update($data);
        }
    }

    public function notifyOrderEvent(int $orderId, string $status, string $timestamp): void
    {
        $dto = new OrderEventDTO($orderId, $status, $timestamp);
        $this->notify($dto);
    }

    public function publishOrderEvent(int $orderId, string $status, string $timestamp): void
    {
        $this->notifyOrderEvent($orderId, $status, $timestamp);
    }
}
