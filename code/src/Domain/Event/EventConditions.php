<?php

declare(strict_types=1);

namespace App\Domain\Event;

use InvalidArgumentException;

/**
 * Набор условий возникновения события
 */
final class EventConditions
{
    /**
     * @var array<string, int|float|string|bool|null>
     */
    private array $conditions;

    /**
     * @param array<string, mixed> $conditions
     */
    public function __construct(array $conditions)
    {
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

    /**
     * @return array<string, int|float|string|bool|null>
     */
    public function all(): array
    {
        return $this->conditions;
    }
}
