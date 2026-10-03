<?php

declare(strict_types=1);

namespace App\Domain\BankReport\ValueObject;

use App\Domain\BankReport\Exception\ReportIdException;

readonly class ReportId
{
    public string $value;

    private function __construct(string $value)
    {
        if ($value === '') {
            ReportIdException::notEmpty();
        }

        $this->value = $value;
    }

    public static function generate(): self
    {
        return new self(uniqid(more_entropy: true));
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }
}
