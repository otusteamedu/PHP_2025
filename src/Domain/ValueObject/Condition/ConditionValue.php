<?php

declare(strict_types=1);

namespace App\Domain\ValueObject\Condition;

class ConditionValue
{
    /**
     * @var string
     */
    private string $value;

    public function __construct(string $value)
    {
        $this->assertValue($value);
        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    private function assertValue(string $value): void
    {
        if (empty($value)) {
            throw new \InvalidArgumentException('Значение условия не должно быть пустым.');
        }
    }
}
