<?php

declare(strict_types=1);

namespace App;

use App\Event\Event;
use App\Event\EventRepositoryInterface;

class EventService
{
    public function __construct(private readonly EventRepositoryInterface $repository) {}

    public function addEvent(int $priority, array $conditions, array $eventData): void
    {
        $event = new Event($priority, $conditions, $eventData);
        $this->repository->save($event);
    }

    public function clearEvents(): void
    {
        $this->repository->clear();
    }

    public function findBestEvent(array $params): ?array
    {
        $event = $this->repository->findBestMatch($params);
        return $event?->getEventData();
    }

    public function getAllEvents(): array
    {
        return array_map(fn($event) => $event->toArray(), $this->repository->findAll());
    }
}