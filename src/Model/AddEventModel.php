<?php

declare(strict_types=1);

namespace App\Model;

use App\Infrastructure\Repository\EventRepositoryInterface;

class AddEventModel
{
    private readonly array $conditions;

    public function __construct(
        private readonly int $id,
        private readonly string $name,
        private readonly int $priority,
        array $conditions,
    ) {
        uksort($conditions, "strnatcmp");
        $this->conditions = $conditions;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function getConditions(): array
    {
        return $this->conditions;
    }

    public function getEventKey(): string
    {
        return EventRepositoryInterface::EVENTS_KEY_PREFIX . ":$this->id:$this->priority";
    }

    public function getPreparedConditions(): string
    {
        $conditions = [];
        foreach ($this->conditions as $key => $value) {
            $conditions[] = "$key=$value";
        }

        return implode('&', $conditions);
    }
}
