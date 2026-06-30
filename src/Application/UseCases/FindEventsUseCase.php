<?php

declare(strict_types=1);

namespace App\Application\UseCases;

use App\Domain\Event;
use App\Domain\EventRepositoryInterface;

final class FindEventsUseCase
{
    public function __construct(
        private EventRepositoryInterface $repository
    )
    {
    }

    public function execute(
        array $params
    ): ?Event
    {
        return $this->repository->findByParams(
            $params
        );
    }
}