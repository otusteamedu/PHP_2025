<?php

declare(strict_types=1);

namespace App\Domain\BankReport\Exception;

class EmailException extends \InvalidArgumentException
{
    public static function notEmpty(): self
    {
        return new self('Email не может быть пустым.');
    }

    public static function invalid(string $value): self
    {
        return new self("Некорректный email: ($value).");
    }
}
