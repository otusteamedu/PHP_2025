<?php

declare(strict_types=1);

namespace Otus\Code\Infrastructure\Notification\Channel;

interface NotificationChannelInterface
{
    public function send(string $recipient, string $message): void;
}
