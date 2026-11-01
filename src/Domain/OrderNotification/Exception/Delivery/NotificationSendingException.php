<?php

declare(strict_types=1);

namespace App\Domain\OrderNotification\Exception\Delivery;

use App\Domain\OrderNotification\Exception\RetryableFailure;

class NotificationSendingException extends \RuntimeException implements RetryableFailure
{
    public static function sendFailedForEmail(\Throwable $e): self
    {
        return new self('Не удалось отправить email-уведомление: ' . $e->getMessage(), previous: $e);
    }

    public static function sendFailedForSms(\Throwable $e): self
    {
        return new self('Не удалось отправить SMS-уведомление: ' . $e->getMessage(), previous: $e);
    }
}
