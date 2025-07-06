<?php

namespace Application\Event;

use Domain\Repository\EventRepositoryInterface;

class EventGetUseCase
{
    private EventRepositoryInterface $eventRepository;

    public function __construct(
        EventRepositoryInterface $eventRepository
    ) {
        $this->eventRepository = $eventRepository;
    }

    public function run(): array {
        return $this->eventRepository->findAll();
    }
}