<?php

declare(strict_types=1);

namespace Queues\Infrastructure\Mailer;

use PHPMailer\PHPMailer\PHPMailer;
use Queues\Application\Interfaces\MailerInterface;

class SmtpMailer implements MailerInterface
{
    public function __construct(
        private readonly string $host,
        private readonly int $port,
        private readonly string $fromEmail = 'noreply@mysite.local',
        private readonly string $fromName = 'Bank Statement Service'
    ) {
    }

    public function send(string $to, string $subject, string $body): void
    {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = $this->host;
        $mail->Port = $this->port;
        $mail->SMTPAuth = false;
        $mail->SMTPSecure = '';
        $mail->SMTPAutoTLS = false;
        $mail->setFrom($this->fromEmail, $this->fromName);
        $mail->addAddress($to);
        $mail->isHTML(false);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->CharSet = 'UTF-8';
        $mail->send();
    }
}
