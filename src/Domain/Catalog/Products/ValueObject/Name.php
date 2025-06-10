<?php
declare(strict_types=1);

namespace Domain\Catalog\Products\ValueObject;

class Name
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
            throw new \InvalidArgumentException('Error Product Name');
        }
    }
}