<?php

declare(strict_types=1);

namespace App\Console\Command;

use App\Console\IO\ConsoleOutput;

final class ESBulkCommand extends ESCommand
{
    public function getName(): string
    {
        return 'app:es-bulk';
    }

    public function getDescription(): string
    {
        return 'Создание индекса Elasticsearch и импорт данных из .json файла (опция --file).';
    }

    public function execute(array $input, ConsoleOutput $output): string
    {
        $file = isset($input['file']) ? (string) $input['file'] : null;

        if ($file === null || $file === '') {
            return $output->error('Опция --file обязательна (путь к файлу в формате .json).');
        }

        try {
            $result = $this->service->bulkImport($file);
        } catch (\Throwable $exception) {
            return $output->error(sprintf('%s failed: %s', $this->getName(), $exception->getMessage()));
        }

        $itemsCount = isset($result['items']) && is_array($result['items']) ? count($result['items']) : 0;
        $hasErrors = (bool) ($result['errors'] ?? false);

        $status = $hasErrors ? 'завершился с ошибками' : 'завершился успешно';

        return $output->writeln(sprintf('%s %s (%d элементов).', $this->getName(), $status, $itemsCount));
    }
}
