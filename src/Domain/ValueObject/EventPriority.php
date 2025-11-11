<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

class EventPriority
{
    /**
     * @var positive-int
     */
    private int $value;

    public function __construct(int $value)
    {
        $this->assertValue($value);
        $this->value = $value;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return (string)$this->value;
    }

    private function assertValue(int $value): void
    {
        if ($value <= 0) {
            throw new \InvalidArgumentException('Приоритет должен быть больше 0');
        }
    }
}
