<?php
declare(strict_types=1);


namespace App\Food\Domain\Aggregate\VO;

use Webmozart\Assert\Assert;

class FoodMass
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
        Assert::greaterThanEq($value, 0, message: 'Weight name cannot be less than 0.');
    }

    public function __toString(): string
    {
        return (string)$this->value;
    }

}