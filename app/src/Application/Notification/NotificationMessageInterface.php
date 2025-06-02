<?php

declare(strict_types=1);

namespace App\Application\Notification;

/**
 * Interface NotificationMessageInterface
 * @package App\Application\Notification
 */
interface NotificationMessageInterface
{
    /**
     * @return string
     */
    public function getText(): string;
}
