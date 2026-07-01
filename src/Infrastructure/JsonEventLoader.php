<?php

declare(strict_types=1);

namespace App\Infrastructure;

use App\Application\Dto\EventDto;

class JsonEventLoader
{
    /**
     * @return EventDto[]
     * @throws \Exception
     */
    public function load(
        string $path
    ): array {
        if ($path === '') {
            throw new \Exception('Путь до json файла не может быть пустым');
        }

        $content = file_get_contents($path);

        $data = json_decode(
            json: $content,
            associative: true
        );

        if ($data === null) {
            throw new \Exception('Файл для импорта не может быть преобразован или глубина вложенности структуры превышает установленный предел');
        }
        if ($data === []) {
            throw new \Exception('В файле нет событий для импорта');
        }

        $events = [];

        foreach ($data as $item) {
            $events[] = new EventDto(
                $item['priority'],
                $item['conditions'],
                $item['event']
            );
        }

        return $events;
    }
}