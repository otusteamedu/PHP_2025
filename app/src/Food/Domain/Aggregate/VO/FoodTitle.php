<?php
declare(strict_types=1);


namespace App\Food\Domain\Aggregate\VO;

use Webmozart\Assert\Assert;

class FoodTitle implements \JsonSerializable
{
    private string $value;
    private const int MIN_LENGTH = 3;
    private const int MAX_LENGTH = 100;

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
        Assert::lengthBetween(
            $value,
            min: self::MIN_LENGTH,
            max: self::MAX_LENGTH,
            message: sprintf('Title should be between %s and %s characters.', self::MIN_LENGTH, self::MAX_LENGTH));
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function jsonSerialize(): string
    {
        return $this->value;
    }
}