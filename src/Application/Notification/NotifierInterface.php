<?php

namespace App\Application\Notification;

interface NotifierInterface
{
    public function notify(string $message): void;
}
