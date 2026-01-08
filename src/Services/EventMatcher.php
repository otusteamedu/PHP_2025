<?php

namespace App\Services;
class EventMatcher {
    private $repository;

    public function __construct(EventRepositoryInterface $repository) {
        $this->repository = $repository;
    }

    public function findBestEvent(array $userParams): ?array {
        $events = $this->repository->getAllEvents();
        $suitableEvents = [];

        foreach ($events as $event) {
            if ($this->isConditionsMet($event['conditions'], $userParams)) {
                $suitableEvents[] = $event;
            }
        }

        if (empty($suitableEvents)) {
            return null;
        }

        // Сортировка по priority в порядке убывания
        usort($suitableEvents, fn($a, $b) => $b['priority'] <=> $a['priority']);

        return $suitableEvents[0];
    }

    private function isConditionsMet(array $conditions, array $params): bool {
        foreach ($conditions as $key => $value) {
            // Если параметра нет в запросе или он не равен условию — событие не подходит
            if (!isset($params[$key]) || $params[$key] != $value) {
                return false;
            }
        }
        return true;
    }
}