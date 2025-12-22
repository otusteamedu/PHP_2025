<?php
declare(strict_types=1);

namespace Dinargab\Homework19\Domain\Notification;

interface NotificationInterface
{
    public function send($email, $subject, $message): void;
}