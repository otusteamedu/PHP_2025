<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Model\AddEventModel;
use App\Domain\Model\GetEventModel;
use App\Domain\Model\SearchEventModel;
use App\Infrastructure\NoSQL\Repository\EventRepositoryFactory;
use App\Infrastructure\NoSQL\Repository\EventRepositoryInterface;

class EventService
{
    private readonly EventRepositoryInterface $eventRepository;

    public function __construct()
    {
        $this->eventRepository = EventRepositoryFactory::create();
    }

    public function addEvent(AddEventModel $addEventModel): bool
    {
        return $this->eventRepository->addEvent($addEventModel);
    }

    public function getEvent(SearchEventModel $searchEventModel): GetEventModel
    {
        return $this->eventRepository->getEvent($searchEventModel);
    }

    public function deleteAllEvents(): bool
    {
        return $this->eventRepository->deleteAllEvents();
    }
}
