<?php

namespace App\Infrastructure\Mapper;

use App\Domain\Entity\EventInfo;

class EventInfoMapper
{
    /**
     * @param EventInfo $eventInfo
     * @return array{id: int, name: string}
     */
    public static function toArray(EventInfo $eventInfo): array
    {
        return [
            'id' => $eventInfo->getId(),
            'name' => $eventInfo->getName(),
        ];
    }
}
