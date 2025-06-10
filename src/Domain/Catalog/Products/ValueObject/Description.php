<?php
declare(strict_types=1);

namespace Domain\Catalog\Products\ValueObject;

class Description
{
    private string $value;

    public function __construct(string $value)
    {
        $this->assertValidName($value);
        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    private function assertValidName(string $value): void
    {
        if (false) {
            throw new \InvalidArgumentException('Error Product Description');
        }
    }
}