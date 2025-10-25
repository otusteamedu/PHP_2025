<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use InvalidArgumentException;

final readonly class RequestStatus
{
    public const string PENDING = 'pending';
    public const string PROCESSING = 'processing';
    public const string COMPLETED = 'completed';
    public const string FAILED = 'failed';

    public function __construct(
        private string $value
    ) {
        if (!in_array($value, [self::PENDING, self::PROCESSING, self::COMPLETED, self::FAILED], true)) {
            throw new InvalidArgumentException("Invalid status: {$value}");
        }
    }

    public static function pending(): self
    {
        return new self(self::PENDING);
    }

    public static function processing(): self
    {
        return new self(self::PROCESSING);
    }

    public static function completed(): self
    {
        return new self(self::COMPLETED);
    }

    public static function failed(): self
    {
        return new self(self::FAILED);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function isPending(): bool
    {
        return $this->value === self::PENDING;
    }

    public function isProcessing(): bool
    {
        return $this->value === self::PROCESSING;
    }

    public function isCompleted(): bool
    {
        return $this->value === self::COMPLETED;
    }

    public function isFailed(): bool
    {
        return $this->value === self::FAILED;
    }

    public function equals(RequestStatus $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
