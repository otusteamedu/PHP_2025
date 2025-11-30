<?php
declare(strict_types=1);

namespace App\Classes;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class MailService
{

    /**
     * @throws Exception
     */
    public function send(string $email): void
    {
        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->Host = $_ENV['MAILHOG_CONTAINER'];
        $mail->Port = $_ENV['MAILHOG_PORT'];
        $mail->SMTPAuth = false;

        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';

        $mail->isHTML(false);
        $mail->ContentType = 'text/plain';

        $mail->setFrom($_ENV['MAIL_FROM'], 'Report Bot');
        $mail->addAddress($email);

        $subject = 'Отчёт готов';
        $mail->Subject = '=?UTF-8?B?' . base64_encode($subject) . '?=';

        $mail->Body = 'Ваш отчёт успешно сгенерирован.';
        $mail->send();
    }
}