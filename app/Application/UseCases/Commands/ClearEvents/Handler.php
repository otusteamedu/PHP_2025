<?php

namespace App\Application\UseCases\Commands\ClearEvents;

use App\Application\EventRepositoryInterface;

class Handler
{
    public function __construct(
        private EventRepositoryInterface $repository,
    ) {
    }

    public function handle(): void
    {
        $this->repository->deleteAll();
    }
}