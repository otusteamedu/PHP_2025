<?php

declare(strict_types=1);

namespace App\Domain\OrderNotification\Exception\OrderSource;

use App\Domain\OrderNotification\Exception\TerminalFailure;

class OrderDataInvalidException extends \RuntimeException implements TerminalFailure
{
    public static function httpError(string $message): self
    {
        return new self($message);
    }

    public static function invalidPayload(string $message): self
    {
        return new self($message);
    }
}
