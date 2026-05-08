<?php

declare(strict_types=1);

namespace App\Application\Console\Controller;

use App\Application\Command\ClearEventsCommand;
use App\Application\Console\InputArguments;
use App\Application\Handler\ClearEventsHandler;
use App\Domain\Service\EventService;

/**
 * Консольный контроллер команды clear
 */
final class ClearEventsController
{
    private ClearEventsHandler $handler;

    public function __construct(EventService $service)
    {
        $this->handler = new ClearEventsHandler($service);
    }

    /**
     * Запускает очистку событий и возвращает данные для вывода
     *
     * @return array<string, mixed>
     */
    public function handle(InputArguments $arguments): array
    {
        $this->handler->handle(new ClearEventsCommand());

        return [
            'status' => 'ok',
            'message' => 'Events cleared.',
        ];
    }
}
