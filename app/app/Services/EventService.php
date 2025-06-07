<?php

namespace App\Services;

use App\Models\Events;

class EventService
{
    public function addEvent(string $eventNumber):Events
    {
        if ($this->getEvent($eventNumber)) {
            throw new \RuntimeException('Event with such number has already exists');
        }

        return Events::create(['number' => $eventNumber]);
    }

    public function getEvent(string $eventNumber):Events|null
    {
        return Events::where('number', $eventNumber)->first();
    }
}
