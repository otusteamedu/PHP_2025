<?php

declare(strict_types=1);

namespace App\Model;

class GetEventModel
{
    public function __construct(
        private readonly string $id,
        private readonly string $name,
        private readonly string $priority,
        private readonly string $conditions,
    ) {
    }

    public function toArray(): array
    {
        $conditions = [];
        foreach (explode('&', $this->conditions) as $condition) {
            [$key, $value] = explode('=', $condition);
            $conditions[$key] = is_numeric($value) ? (int) $value : $value;
        }

        return [
            'id' => (int) $this->id,
            'name' => $this->name,
            'priority' => (int) $this->priority,
            'conditions' => $conditions,
        ];
    }
}
