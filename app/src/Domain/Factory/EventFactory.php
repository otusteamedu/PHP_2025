<?php
declare(strict_types=1);


namespace App\Domain\Factory;

use App\Domain\Entity\Event;

class EventFactory
{
    public function create(int $priority, string $name, array $conditions): Event
    {
        $event = new Event($priority, $name);
        foreach ($conditions as $param => $value) {
            $event->addCondition($param, $value);
        }
        return $event;
    }

}