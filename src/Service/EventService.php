<?php
namespace App\Service;

use App\Repository\EventRepositoryInterface;

class EventService {
    private EventRepositoryInterface $repository;

    public function __construct(EventRepositoryInterface $repository) {
        $this->repository = $repository;
    }

    public function addEvent(array $event): void {
        $this->repository->addEvent($event);
    }

    public function clearEvents(): void {
        $this->repository->clearEvents();
    }

    public function getBestMatch(array $params): ?array {
        return $this->repository->findMatchingEvent($params);
    }
}
