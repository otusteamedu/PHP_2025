<?php

namespace App\Domain\Entities;

use App\Domain\ValueObjects\Conditions;
use App\Domain\ValueObjects\EventName;
use App\Domain\ValueObjects\Priority;

class Event
{
    public function __construct(  
        private EventName $eventName,  
        private Priority $priority,
        private Conditions $conditions
    )  {} 

    public function getEventName()
    {
        return $this->eventName;
    }

    public function getPriority()
    {
        return $this->priority;
    }
    public function getConditions()
    {
        return $this->conditions;
    }
}