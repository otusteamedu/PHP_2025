<?php

declare(strict_types=1);

namespace App\Console\Command;

use App\Console\IO\ConsoleOutput;

final class ESPingCommand extends ESCommand
{
    public function getName(): string
    {
        return 'app:es-ping';
    }

    public function getDescription(): string
    {
        return 'Проверка соединения с сервером Elasticsearch.';
    }

    public function execute(array $input, ConsoleOutput $output): string
    {
        if ($this->service->ping()) {
            return $output->writeln('Сервер Elasticsearch доступен.');
        }

        return $output->error('Сервер Elasticsearch недоступен.');
    }
}
