<?php

declare(strict_types=1);

namespace App\Application\Handler;

use App\Application\Command\ClearEventsCommand;
use App\Domain\Service\EventService;

/**
 * Обработчик команды очистки событий
 */
final class ClearEventsHandler
{
    public function __construct(private readonly EventService $eventService)
    {
    }

    /**
     * Запускает очистку хранилища событий
     */
    public function handle(ClearEventsCommand $command): void
    {
        $this->eventService->clear();
    }
}
