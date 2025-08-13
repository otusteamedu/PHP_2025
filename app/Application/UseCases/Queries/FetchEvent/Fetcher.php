<?php

namespace App\Application\UseCases\Queries\FetchEvent;

use App\Application\EventRepositoryInterface;

class Fetcher
{
    public function __construct(
        private EventRepositoryInterface $repository,
    ) {}

    public function fetch(Dto $dto): string
    {
        $events = $this->repository->fetchAll();

        $filteredEvents = array_filter($events, function($item) use($dto) {
            $conditions = $item->getConditions()->toArray();
            $result = true;
            foreach ($dto->params as $par => $value) {
                if (!isset($conditions[$par]) || $conditions[$par] != $value) {
                    $result = false;
                    break;
                }
            }
            return $result;
        });

        if (empty($filteredEvents)) {
            return 'Событий нет';
        }

        $event = '';
        $priority = 0;
        foreach ($filteredEvents as $ev) {
            $currentPriority = $ev->getPriority()->toInt();
            if ($currentPriority > $priority) {
                $priority = $currentPriority;
                $event = $ev->getEventName()->toString();
            }
        }

        return $event;
    }
}