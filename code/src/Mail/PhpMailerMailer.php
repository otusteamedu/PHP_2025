<?php

declare(strict_types=1);

namespace App\Mail;

use PHPMailer\PHPMailer\PHPMailer;

/**
 * Сервис отправки email.
 */
final class PhpMailerMailer implements MailerInterface
{
    /**
     * @param string $host SMTP-хост.
     * @param int $port SMTP-порт.
     * @param string $username Имя пользователя SMTP.
     * @param string $password Пароль SMTP.
     * @param string $encryption Тип шифрования SMTP.
     * @param string $fromEmail Email отправителя.
     * @param string $fromName Имя отправителя.
     */
    public function __construct(
        private readonly string $host,
        private readonly int $port,
        private readonly string $username,
        private readonly string $password,
        private readonly string $encryption,
        private readonly string $fromEmail,
        private readonly string $fromName,
    ) {
    }

    /**
     * Отправляет текстовое email-сообщение.
     *
     * @param string $to Email получателя.
     * @param string $subject Тема письма.
     * @param string $body Текст письма.
     */
    public function send(string $to, string $subject, string $body): void
    {
        $mailer = new PHPMailer(true);
        $mailer->CharSet = 'UTF-8';
        $mailer->isSMTP();
        $mailer->Host = $this->host;
        $mailer->Port = $this->port;
        $mailer->SMTPAutoTLS = $this->encryption !== '';

        if ($this->username !== '') {
            $mailer->SMTPAuth = true;
            $mailer->Username = $this->username;
            $mailer->Password = $this->password;
        }

        if ($this->encryption !== '') {
            $mailer->SMTPSecure = $this->encryption;
        }

        $mailer->setFrom($this->fromEmail, $this->fromName);
        $mailer->addAddress($to);
        $mailer->isHTML(false);
        $mailer->Subject = $subject;
        $mailer->Body = $body;

        $mailer->send();
    }
}
