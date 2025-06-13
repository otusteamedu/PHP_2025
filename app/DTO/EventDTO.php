<?php

namespace App\DTO;

class EventDTO
{
    public ?int $priority;
    public array $event;
    public array $params;

    public function __construct(
        ?int $priority = null,
        array $event = [],
        array $params = []
    ) {
        $this->priority = $priority;
        $this->event = $event;
        $this->params = $params;
    }
}