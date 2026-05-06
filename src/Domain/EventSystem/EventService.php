<?php

declare(strict_types=1);

namespace App\Domain\EventSystem;

use App\Domain\EventSystem\Interface\EventRepositoryInterface;
use App\Domain\EventSystem\Model\AddEventModel;
use App\Domain\EventSystem\Model\GetEventModel;
use App\Domain\EventSystem\Model\SearchEventModel;
use App\Infrastructure\Storage\KeyValue\Factory\EventRepositoryFactory;

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
