<?php
declare(strict_types=1);


namespace App\Infrastructure\Mapper;

use App\Domain\Entity\Event;

class EventMapper
{
    public function map(array $data): Event
    {
        $event = new Event(
            $data['priority'] ? (int)$data['priority'] : null,
            $data['name'] ?? null,
            $data['id']);
        $conditions = $data['conditions'] ? json_decode($data['conditions']) : [];
        foreach ($conditions as $param => $value) {
            $event->addCondition($param, $value);
        }

        return $event;
    }

}