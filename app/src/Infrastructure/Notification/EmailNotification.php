<?php

declare(strict_types=1);

namespace App\Infrastructure\Notification;

use App\Application\Notification\NotificationInterface;
use App\Application\Notification\NotificationMessageInterface;
use DomainException;

/**
 * Class EmailNotification
 * @package App\Infrastructure\Notification
 */
class EmailNotification implements NotificationInterface
{
    /**
     * @param EmailNotificationMessage $message
     * @return void
     */
    public function send(NotificationMessageInterface $message): void
    {
        $sendResult = mail(
            $message->getEmail(),
            $message->getSubject(),
            $message->getText()
        );

        if (!$sendResult) {
            throw new DomainException('Error sending email notification.');
        }
    }
}
