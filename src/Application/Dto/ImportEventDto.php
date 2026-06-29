<?php

declare(strict_types=1);

namespace App\Application\Dto;

final readonly class ImportEventDto
{
    public function __construct(
        public int $priority,
        public array $conditions,
        public array $eventData,
    )
    {
    }
}