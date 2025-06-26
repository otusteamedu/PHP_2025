<?php

namespace Consumer\Application\Mailer;

use Consumer\Domain\Mailer\MailerInterface;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class PhpMailerService implements MailerInterface
{
    private PHPMailer $mail;

    /**
     * @throws Exception
     */
    public function __construct() {
        $mail = new PHPMailer(true);
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $mail->isSMTP();
        $mail->SMTPSecure = '';
        $mail->Host = 'maildev';
        $mail->SMTPAuth = false;
        $mail->Port = 25;
        $mail->setLanguage('ru');
        $mail->CharSet = 'UTF-8';

        $connected = $mail->smtpConnect();
        if (!$connected) {
            throw new Exception("Не может подключиться к SMTP серверу");
        }

        $this->mail = $mail;
    }

    /**
     * @param string $subject
     * @param string $body
     * @param string $to
     * @param string|null $from
     * @return void
     * @throws Exception
     */
    public function mail(
        string $subject,
        string $body,
        string $to,
        ?string $from
    ): void {
        $mail = $this->mail;
        $mail->setFrom($from ?? getenv('EMAIL_TO'));
        $mail->addAddress($to);
        $mail->isHTML();
        $mail->Subject = $subject;
        $mail->Body = $body;

        $sent = $mail->send();
        if (!$sent) {
            throw new Exception("Ошибка отправки письма: $mail->ErrorInfo");
        }
    }
}