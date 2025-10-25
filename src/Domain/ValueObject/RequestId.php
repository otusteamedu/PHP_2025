<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use InvalidArgumentException;

final readonly class RequestId
{
    public function __construct(
        private string $value
    ) {
        if (empty($value)) {
            throw new InvalidArgumentException('Request ID cannot be empty');
        }
    }

    public static function generate(): self
    {
        return new self('req_' . uniqid('', true) . '.' . bin2hex(random_bytes(8)));
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(RequestId $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
