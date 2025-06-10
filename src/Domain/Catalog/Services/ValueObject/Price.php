<?php
declare(strict_types=1);

namespace Domain\Catalog\Services\ValueObject;

class Price
{
    private float $value;

    public function __construct(float $value)
    {
        $this->assertValidName($value);
        $this->value = $value;
    }

    public function getValue(): float
    {
        return $this->value;
    }

    private function assertValidName(float $value): void
    {
        if (false) {
            throw new \InvalidArgumentException('Error Services Price');
        }
    }
}
