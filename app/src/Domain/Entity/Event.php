<?php
declare(strict_types=1);


namespace App\Domain\Entity;

use App\Domain\Service\UuidService;

class Event implements \JsonSerializable
{
    private array $conditions = [];
    private string $id;

    public function __construct(
        private readonly int    $priority,
        private readonly string $name,
        ?string                 $id = null,
    )
    {
        if (!$id) {
            $this->id = UuidService::generate();
        } else {
            $this->id = $id;
        }
    }

    public function getConditions(): array
    {
        return $this->conditions;
    }

    public function addCondition(string $param, int|string $value): void
    {
        $this->conditions[$param] = $value;
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


    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }
}