<?php

declare(strict_types=1);

namespace App\Console\Command;

use App\Console\IO\ConsoleOutput;

final class ESDeleteIndexCommand extends ESCommand
{
    public function getName(): string
    {
        return 'app:es-drop-index';
    }

    public function getDescription(): string
    {
        return 'Удаляет индекс в Elasticsearch (опция --name, по умолчанию otus-shop).';
    }

    public function execute(array $input, ConsoleOutput $output): string
    {
        $name = isset($input['name']) ? (string)$input['name'] : null;

        try {
            $result = $this->service->deleteIndex($name);
        } catch (\Throwable $e) {
            return $output->error(sprintf('Возникла ошибка при удалении индекса: %s', $e->getMessage()));
        }

        $ack = $result['acknowledged'] ?? null;
        $idx = $result['index'] ?? ($name ?? 'otus-shop');

        return $output->writeln(sprintf('Запрос на удаление индекса "%s" подтверждён: %s', $idx, $ack ? 'да' : 'нет'));
    }
}
