<?php

declare(strict_types=1);

namespace App\Infrastructure;

use App\Application\Dto\ImportEventDto;

class JsonEventLoader
{
    /**
     * @return ImportEventDto[]
     */
    public function load(
        string $path
    ): array {
        $content = file_get_contents($path);

        $data = json_decode(
            json: $content,
            associative:  true,
            flags: JSON_THROW_ON_ERROR
        );

        $events = [];

        foreach ($data as $item) {
            $events[] = new ImportEventDto(
                $item['priority'],
                $item['conditions'],
                $item['event']
            );
        }

        return $events;
    }
}