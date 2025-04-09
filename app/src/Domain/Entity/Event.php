<?php
declare(strict_types=1);


namespace App\Domain\Entity;

use App\Domain\Service\UuidService;

class Event
{
    private array $conditions;
    private string $id;

    public function __construct(
        private readonly int    $priority,
        private readonly string $name,
        Condition               ...$conditions
    )
    {
        $this->id = UuidService::generate();
        $this->conditions = $conditions;
    }

    public function getConditions(): array
    {
        return $this->conditions;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function getName(): string
    {
        return $this->name;
    }

}