<?php

declare(strict_types=1);

namespace App\Infrastructure\Notification;

use App\Application\Notification\NotificationMessageInterface;

/**
 * Class EmailNotificationMessage
 * @package App\Infrastructure\Notification
 */
readonly class EmailNotificationMessage implements NotificationMessageInterface
{
    /**
     * @param string $email
     * @param string $subject
     * @param string $text
     */
    public function __construct(
        private string $email,
        private string $subject,
        private string $text
    ) {
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }
}
