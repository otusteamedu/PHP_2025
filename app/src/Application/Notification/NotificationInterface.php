<?php

declare(strict_types=1);

namespace App\Application\Notification;

/**
 * Interface NotificationInterface
 * @package App\Application\Notification
 */
interface NotificationInterface
{
    /**
     * @param NotificationMessageInterface $message
     * @return void
     */
    public function send(NotificationMessageInterface $message): void;
}
