<?php

declare(strict_types=1);

namespace Otus\Code\Infrastructure\Notification\Observer;

final class SmsOrderObserver extends AbstractOrderNotificationObserver
{
    protected function messagePrefix(): string
    {
        return 'SMS notification';
    }
}
