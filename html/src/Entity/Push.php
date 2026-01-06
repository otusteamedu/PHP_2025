<?php

declare(strict_types=1);

namespace Otus\Cache\Entity;

readonly class Push
{
    /**
     * @param int $priority
     * @param Conditions $conditions
     */
    public function __construct(public int $priority, public Conditions $conditions)
    {
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'priority' => $this->priority,
            'conditions' => $this->conditions->toArray(),
        ];
    }
}
