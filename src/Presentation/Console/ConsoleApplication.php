<?php

declare(strict_types=1);

namespace App\Presentation\Console;

use App\Application\UseCases\AddEventUseCase;
use App\Application\UseCases\ClearEventsUseCase;
use App\Application\UseCases\FindEventsUseCase;
use App\Application\UseCases\ImportEventsUseCase;
use App\Infrastructure\EventRepository;
use App\Infrastructure\JsonEventLoader;
use App\Infrastructure\RedisClient;

class ConsoleApplication
{
    private ImportEventsUseCase $importEvents;
    private AddEventUseCase $addEvent;
    private FindEventsUseCase $findEvent;
    private ClearEventsUseCase $clearEvents;

    public function __construct( )
    {
        $redisClient = new RedisClient();
        $repository = new EventRepository($redisClient);

        $this->importEvents = new ImportEventsUseCase(
            new JsonEventLoader(),
            $repository
        );

        $this->clearEvents = new ClearEventsUseCase($repository);
        $this->addEvent = new AddEventUseCase($repository);
        $this->findEvent = new FindEventsUseCase($repository);
    }

    public function run(): void
    {
        while (true) {
            echo PHP_EOL;
            echo "1. Импортировать события из JSON\n";
            echo "2. Добавить событие\n";
            echo "3. Очистить события\n";
            echo "4. Найти событие\n";
            echo "5. Выход\n";

            $action = trim(
                readline('> ')
            );

            switch ($action) {
                case '1':
                    try {
                        $this->importEvents->execute();
                        echo "События успешно импортированы.\n";
                    } catch (\Throwable $e) {
                        echo $e->getMessage() . PHP_EOL;
                    }
                    break;
                case '2':
                    $eventDto = (new ConsoleEventInput(new ParamsParser()))->getEventData();
                    try {
                        $this->addEvent->execute($eventDto);
                        echo 'События успешно добавлено.' . PHP_EOL;
                    } catch (\RedisException $e)        {
                        echo $e->getMessage() . PHP_EOL;
                    }
                    break;
                case '3':
                    try {
                        $this->clearEvents->execute();
                        echo 'События успешно импортированы.' . PHP_EOL;
                    } catch (\RedisException $e)        {
                        echo $e->getMessage() . PHP_EOL;
                    }
                    break;
                case '4':
                    $eventParams = (new ConsoleEventInput(new ParamsParser()))->getEventParams();
                    $event = $this->findEvent->execute($eventParams);

                    if ($event === null) {
                        echo 'Событие не найдено.' . PHP_EOL;
                        break;
                    }
                    echo 'Найдено событие с наибольшим приоритетом:' . PHP_EOL;

                    var_dump($event);

                    break;
                case '5':
                    return;
                default:
                    echo "Неизвестная команда\n";
            }
        }
    }
}