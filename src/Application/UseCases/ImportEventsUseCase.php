<?php

declare(strict_types=1);

namespace App\Application\UseCases;

use App\Domain\Event;
use App\Domain\EventRepositoryInterface;
use App\Infrastructure\JsonEventLoader;

final class ImportEventsUseCase
{
    public function __construct(
        private JsonEventLoader $loader,
        private EventRepositoryInterface $repository
    )
    {
    }

    public function execute(): void
    {
        $path = trim(
            readline('Введите путь к json: ')
        );

        try {
            $eventsDto = $this->loader->load($path);

            foreach ($eventsDto as $eventDto) {
                $this->repository->save(
                    new Event(
                        id: $this->repository->nextId(),
                        priority: $eventDto->priority,
                        conditions: $eventDto->conditions,
                        eventData: $eventDto->eventData
                    )
                );
            }

            echo "События успешно импортированы.\n";
        } catch (\Throwable $e) {
            echo $e->getMessage() . PHP_EOL;
        }

    }
}