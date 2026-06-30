<?php

declare(strict_types=1);

namespace App\Presentation\Console;

use App\Application\Dto\EventDto;

final readonly class ConsoleEventInput
{
    public function __construct(
        private ParamsParser $parser
    ) {
    }

    public function getEventData(): EventDto
    {
        echo PHP_EOL;
        echo "Создание события\n";
        echo "=================\n";

        $priority = $this->readPriority();

        $conditions = $this->readConditions();

        $eventData = $this->readEventData();

        return new EventDto(
            $priority,
            $conditions,
            $eventData
        );
    }

    private function readPriority(): int
    {
        while (true) {

            echo PHP_EOL;
            echo "Введите приоритет.\n";
            echo "Пример: 3000\n";

            $priority = trim(readline("> "));

            if (is_numeric($priority)) {
                return (int)$priority;
            }

            echo "Некорректный приоритет.\n";
        }
    }

    private function readConditions(): array
    {
        echo PHP_EOL;
        echo "Введите условия.\n";
        echo "Формат:\n";
        echo "param1 1, param2 2\n";

        return $this->readManyParams();
    }

    private function readEventData(): array
    {
        echo PHP_EOL;
        echo "Введите данные события.\n";
        echo "Формат:\n";
        echo "title Покупка, type BUY\n";

        return $this->readManyParams();
    }

    private function readManyParams(): array
    {
        return $this->parser->parse(
            readline("> ")
        );
    }
}