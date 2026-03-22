<?php

declare(strict_types=1);

namespace Otus\Code\Infrastructure\Notification\Channel;

final class SmsChannel implements NotificationChannelInterface
{
    public function send(string $recipient, string $message): void
    {
        echo '[SMS to ' . $recipient . '] ' . $message . "\n";
    }
}
