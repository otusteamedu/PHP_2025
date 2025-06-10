<?php
declare(strict_types=1);

namespace Domain\Licenses\ValueObject;

class Period
{
    private int $value;

    public function __construct(int $value)
    {
        $this->assertValidName($value);
        $this->value = $value;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    private function assertValidName(int $value): void
    {
        if (false) {
            throw new \InvalidArgumentException('Error Licenses Period');
        }
    }
}