<?php

namespace App\Application\UseCases\Commands\AddEvent;

use App\Application\EventRepositoryInterface;
use App\Domain\Entities\Event;
use App\Domain\ValueObjects\Conditions;
use App\Domain\ValueObjects\EventName;
use App\Domain\ValueObjects\Priority;

class Handler
{
    public function __construct(
        private EventRepositoryInterface $repository,
    ) {
    }

    public function handle(Dto $dto): void
    {
        $event = new Event(
            new EventName($dto->event),
            new Priority($dto->priority),
            new Conditions($dto->conditions)
        );

        $this->repository->save($event);
    }
}