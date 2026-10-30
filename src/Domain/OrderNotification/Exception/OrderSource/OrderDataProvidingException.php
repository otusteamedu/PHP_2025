<?php

declare(strict_types=1);

namespace App\Domain\OrderNotification\Exception\OrderSource;

use App\Domain\OrderNotification\Exception\RetryableFailure;

class OrderDataProvidingException extends \RuntimeException implements RetryableFailure
{
    public static function unreachable(\Throwable $e): self
    {
        return new self('Сервис заказов временно недоступен.', previous: $e);
    }
}
