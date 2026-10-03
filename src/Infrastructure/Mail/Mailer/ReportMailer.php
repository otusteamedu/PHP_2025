<?php

declare(strict_types=1);

namespace App\Infrastructure\Mail\Mailer;

use App\Domain\BankReport\ValueObject\Email;
use App\Domain\BankReport\ValueObject\Report;
use App\Infrastructure\Mail\Exception\ReportMailerException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email as MimeEmail;

class ReportMailer implements ReportMailerInterface
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly string $fromEmail,
    ) {
    }

    public function send(Report $report, Email $email): void
    {
        $message = new MimeEmail()
            ->from($this->fromEmail)
            ->to($email->value)
            ->subject($report->header)
            ->text($report->body);

        try {
            $this->mailer->send($message);
        } catch (TransportExceptionInterface $e) {
            throw ReportMailerException::transportError($e);
        }
    }
}
