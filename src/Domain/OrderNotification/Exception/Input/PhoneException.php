<?php

declare(strict_types=1);

namespace App\Domain\OrderNotification\Exception\Input;

use App\Domain\Shared\Exception\InvalidInputException;

class PhoneException extends InvalidInputException
{
    public static function notEmpty(): self
    {
        return new self('Номер телефона не может быть пустым.');
    }

    public static function invalid(string $value): self
    {
        return new self("Некорректный номер телефона ($value).");
    }
}
