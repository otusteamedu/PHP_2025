<?php

declare(strict_types=1);

namespace App\Application\Command;

use InvalidArgumentException;

/**
 * Команда добавления события из входных аргументов консоли
 */
final class AddEventCommand
{
    /**
     * @var array<string, int|float|string|bool|null>
     */
    private array $conditions;

    /**
     * @param array<string, mixed> $conditions
     * @param array<string, mixed> $eventPayload
     */
    public function __construct(
        private readonly int $priority,
        array $conditions,
        private readonly array $eventPayload,
    ) {
        foreach ($conditions as $name => $value) {
            if (!is_string($name) || $name === '') {
                throw new InvalidArgumentException('Condition name must be a non-empty string.');
            }

            if (!is_scalar($value) && $value !== null) {
                throw new InvalidArgumentException('Condition value must be scalar or null.');
            }
        }

        $this->conditions = $conditions;
    }

    public function priority(): int
    {
        return $this->priority;
    }

    /**
     * @return array<string, int|float|string|bool|null>
     */
    public function conditions(): array
    {
        return $this->conditions;
    }

    /**
     * @return array<string, mixed>
     */
    public function eventPayload(): array
    {
        return $this->eventPayload;
    }
}
