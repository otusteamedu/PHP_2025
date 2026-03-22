<?php

declare(strict_types=1);

namespace Otus\Code\Infrastructure\Notification\Channel;

final class PushChannel implements NotificationChannelInterface
{
    public function send(string $recipient, string $message): void
    {
        echo '[PUSH to ' . $recipient . '] ' . $message . "\n";
    }
}
