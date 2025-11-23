<?php

declare(strict_types=1);

namespace App\Domain;

final class Email
{
    private function __construct(public string $value)
    {
    }

    public static function create(string $value): ?self
    {
        $value = trim($value);

        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return null;
        }

        return new self(strtolower($value));
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
