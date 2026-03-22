<?php

declare(strict_types=1);

namespace Otus\Code\Infrastructure\Notification\Observer;

final class PushOrderObserver extends AbstractOrderNotificationObserver
{
    protected function messagePrefix(): string
    {
        return 'Push notification';
    }
}
