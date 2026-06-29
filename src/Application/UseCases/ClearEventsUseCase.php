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
    public function execute(): void
    {
        try {
            $this->repository->clear();
            echo 'События успешно импортированы.' . PHP_EOL;
        } catch (\RedisException $e)        {
            echo $e->getMessage() . PHP_EOL;
        }
    }
}