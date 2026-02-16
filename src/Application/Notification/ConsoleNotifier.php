<?php

namespace App\Application\Notification;

class ConsoleNotifier implements NotifierInterface
{
    public function notify(string $message): void
    {
        echo $message . PHP_EOL;
    }
}
