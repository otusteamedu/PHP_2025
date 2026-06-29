<?php

declare(strict_types=1);

namespace App\Presentation\Console;

use App\Application\UseCases\ImportEventsUseCase;
use App\Infrastructure\EventRepository;
use App\Infrastructure\JsonEventLoader;
use App\Infrastructure\RedisClient;

class ConsoleApplication
{
    private ImportEventsUseCase $importEvents;
    public function __construct( )
    {
        $redisClient = new RedisClient();
        $repository = new EventRepository($redisClient);

        $this->importEvents = new ImportEventsUseCase(
            new JsonEventLoader(),
            $repository
        );
    }

    public function run(): void
    {
        while (true) {
            echo PHP_EOL;
            echo "1. Импортировать события из JSON\n";
            echo "5. Выход\n";

            $action = trim(
                readline('> ')
            );

            switch ($action) {
                case '1':
                    $this->importEvents->execute();
                    break;
                case '5':
                    return;
                default:
                    echo "Неизвестная команда\n";
            }
        }
    }
}