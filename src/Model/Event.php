<?php

declare(strict_types=1);

namespace Dinargab\Homework11\Model;

class Event
{
    private int $priority = 0;
    private array $conditions = [];
    private string $name;

    public function __construct(string $name, array $conditions, $priority)
    {
        $this->priority = $priority;
        $this->conditions = $conditions;
        $this->name = $name;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function getConditions(): array
    {
        return $this->conditions;
    }

    public function getName(): string
    {
        return $this->name;
    }
}