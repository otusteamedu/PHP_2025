<?php

declare(strict_types=1);

namespace App\Application\UseCases;

use App\Application\Dto\EventDto;
use App\Domain\Event;
use App\Domain\EventRepositoryInterface;

final readonly class AddEventUseCase
{
    public function __construct(
        private EventRepositoryInterface $repository
    )
    {
    }

    /**
     * @throws \RedisException
     */
    public function execute(
        EventDto $dto
    ): void {
        $event = new Event(
            $this->repository->nextId(),
            $dto->priority,
            $dto->conditions,
            $dto->eventData
        );

        $this->repository->save($event);
    }
}