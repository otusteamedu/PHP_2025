<?php

declare(strict_types=1);

namespace App\Domain\OrderNotification\Exception\Input;

use App\Domain\Shared\Exception\InvalidInputException;

class OrderIdException extends InvalidInputException
{
    public static function lessThanZero(): self
    {
        return new self('Id заказа должен быть положительным числом.');
    }
}
