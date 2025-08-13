<?php

namespace App\Application\UseCases\Commands\AddEvent;

class Dto
{
    public function __construct(
        public readonly string $event,
        public readonly int $priority,
        public readonly array $conditions
    ) {}
}