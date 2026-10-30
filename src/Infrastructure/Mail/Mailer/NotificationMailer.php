<?php

declare(strict_types=1);

namespace App\Infrastructure\Mail\Mailer;

use App\Domain\OrderNotification\Contract\NotificationMailerInterface;
use App\Domain\OrderNotification\Exception\Delivery\NotificationSendingException;
use App\Domain\OrderNotification\ValueObject\Message\EmailMessage;
use App\Domain\OrderNotification\ValueObject\Recipient\Email;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email as MimeEmail;

class NotificationMailer implements NotificationMailerInterface
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly string $fromEmail,
    ) {
    }

    public function send(EmailMessage $message, Email $email): void
    {
        $message = new MimeEmail()
            ->from($this->fromEmail)
            ->to($email->value())
            ->subject($message->getSubject())
            ->text($message->getBody());

        try {
            $this->mailer->send($message);
        } catch (TransportExceptionInterface $e) {
            throw NotificationSendingException::sendFailedForEmail($e);
        }
    }
}
