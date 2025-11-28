<?php
declare(strict_types=1);

namespace Dinargab\Homework19\Infrastructure\Service\Notification;


use PHPMailer\PHPMailer\PHPMailer;

class EmailClient
{
    private PHPMailer $mailer;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);
        $host = getenv('SMTP_HOST');
        $username = getenv('SMTP_USERNAME');
        $password = getenv('SMTP_PASSWORD');
        $port = getenv('SMTP_PORT');
        $senderName = getenv('SMTP_SENDER_NAME');

        $this->mailer->isSMTP();
        $this->mailer->Host = $host;
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = $username;
        $this->mailer->Password = $password;
        $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $this->mailer->Port = $port;
        $this->mailer->setFrom($username, $senderName);
    }

    public function getMailer(): PHPMailer
    {
        return $this->mailer;
    }
}