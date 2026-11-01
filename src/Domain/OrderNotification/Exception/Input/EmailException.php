<?php

declare(strict_types=1);

namespace App\Domain\OrderNotification\Exception\Input;

use App\Domain\Shared\Exception\InvalidInputException;

class EmailException extends InvalidInputException
{
    public static function notEmpty(): self
    {
        return new self('Email не может быть пустым.');
    }

    public static function invalid(string $value): self
    {
        return new self("Некорректный email ($value).");
    }
}
