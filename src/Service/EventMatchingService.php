<?php
declare(strict_types=1);

namespace App\Service;

use App\Model\Event;
use App\Repository\EventRepositoryInterface;

class EventMatchingService
{
    private EventRepositoryInterface $repository;

    /**
     * @param EventRepositoryInterface $repository
     */
    public function __construct(EventRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param array $params
     * @return Event|null
     */
    public function findBestMatch(array $params): ?Event
    {
        $allEvents = $this->repository->findAll();

        $matchingEvents = [];
        foreach ($allEvents as $event) {
            if ($this->matchesConditions($event->getConditions(), $params)) {
                $matchingEvents[] = $event;
            }
        }

        if (empty($matchingEvents)) {
            return null;
        }

        usort($matchingEvents, fn($a, $b) => $b->getPriority() <=> $a->getPriority());

        return $matchingEvents[0];
    }

    /**
     * @param array $conditions
     * @param array $params
     * @return bool
     */
    private function matchesConditions(array $conditions, array $params): bool
    {
        foreach ($conditions as $key => $value) {
            if (!isset($params[$key]) || $params[$key] !== $value) {
                return false;
            }
        }

        return true;
    }
}
