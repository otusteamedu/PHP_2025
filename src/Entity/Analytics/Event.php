<?php

namespace App\Entity\Analytics;

class Event
{
    public function __construct(
        public readonly string $eventName,
        public array $conditions,
        public readonly int $priority,
    ) {}

}