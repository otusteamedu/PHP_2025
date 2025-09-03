<?php

namespace App\Infrastructure\Notifications;

use PHPMailer\PHPMailer\PHPMailer;
use App\Application\UseCases\Commands\NotificationSenderInterface;

class EmailSender implements NotificationSenderInterface
{
    public function send($address, $startDate, $finishDate): void
    {
        $host = getenv('SMTP_HOST');
        $username = getenv('SMTP_USERNAME');
        $password = getenv('SMTP_PASSWORD');
        $port = getenv('SMTP_PORT');

        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = $host;
        $mail->SMTPAuth = true;
        $mail->Username = $username;
        $mail->Password = $password;
        $mail->Port = $port;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;

        $mail->setFrom($username, 'Mailer');
        $mail->addAddress($address);
        $mail->addReplyTo($username);

        $mail->isHTML(true);
        $mail->Subject = 'Банковская выписка';
        $mail->Body = "<h1>Банковская выписка</h1><div>Даты: $startDate - $finishDate</div><div>Здесь должны быть данные</div>";
        $mail->AltBody = "Банковская выписка за даты: $startDate - $finishDate\nЗдесь должны быть данные";

        $mail->send();
    }
}