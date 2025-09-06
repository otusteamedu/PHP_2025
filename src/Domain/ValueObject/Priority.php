<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use InvalidArgumentException;

final readonly class Priority
{
    public const string LOW = 'low';
    public const string NORMAL = 'normal';
    public const string HIGH = 'high';

    public function __construct(
        private string $value
    ) {
        if (!in_array($value, [self::LOW, self::NORMAL, self::HIGH], true)) {
            throw new InvalidArgumentException("Invalid priority: {$value}");
        }
    }

    public static function low(): self
    {
        return new self(self::LOW);
    }

    public static function normal(): self
    {
        return new self(self::NORMAL);
    }

    public static function high(): self
    {
        return new self(self::HIGH);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(Priority $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
