<?php
declare(strict_types=1);


namespace App\Application\DTO;

class EventDTO
{
    public ?string $id;
    public ?int $priority;
    public ?string $name;
    public array $conditions = [];
}