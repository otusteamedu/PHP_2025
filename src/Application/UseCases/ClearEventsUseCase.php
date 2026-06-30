<?php

declare(strict_types=1);

namespace App\Application\UseCases;

use App\Domain\EventRepositoryInterface;

final readonly class ClearEventsUseCase
{
    public function __construct(
        private EventRepositoryInterface $repository
    )
    {
    }

    /**
     * @throws \RedisException
     */
    public function execute(): void
    {
        $this->repository->clear();
    }
}