<?php
declare(strict_types=1);

namespace Dinargab\Homework19\Infrastructure\Service\Notification;

use Dinargab\Homework19\Domain\Notification\NotificationInterface;

class EmailNotification implements NotificationInterface
{

    public function __construct(
        private EmailClient $emailClient,
    )
    {

    }

    public function send($email, $subject, $message): void
    {
        $mailer = $this->emailClient->getMailer();
        $mailer->addAddress($email);
        $mailer->Subject = $subject;
        $mailer->Body = $message;
        $mailer->send();
    }
}