<?php

declare(strict_types=1);

namespace App\Domain\OrderNotification\Exception\Input;

use App\Domain\OrderNotification\Exception\TerminalFailure;
use App\Domain\Shared\Exception\InvalidInputException;

class TaskIdException extends InvalidInputException implements TerminalFailure
{
    public static function lessThanZero(): self
    {
        return new self('Id задачи должен быть положительным числом.');
    }
}
