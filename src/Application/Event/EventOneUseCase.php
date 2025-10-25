<?php

namespace Application\Event;

use Domain\Entity\Event;
use Domain\Repository\EventRepositoryInterface;

class EventOneUseCase
{
    private EventRepositoryInterface $eventRepository;

    public function __construct(
        EventRepositoryInterface $eventRepository
    ) {
        $this->eventRepository = $eventRepository;
    }

    public function run(int $id): Event {
        return $this->eventRepository->findById($id);
    }
}