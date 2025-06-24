<?php

namespace Consumer\Service;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class MailSender
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
     * @throws Exception
     */
    public function send(array $data): void {
        $mail = $this->mail;
        $mail->setFrom(getenv('EMAIL_TO'));
        $mail->addAddress(getenv('EMAIL_FROM'));
        $mail->isHTML();
        $mail->Subject = 'Данные выписки из банка';
        $mail->Body =
            "<b>Банк</b>: {$data['bank']}<br>" .
            "<b>Счет</b>: {$data['account']}<br>" .
            "<b>БИК</b>: {$data['bik']}<br>" .
            "<b>Клиент</b>: {$data['client']}<br>";

        $sent = $mail->send();
        if (!$sent) {
            throw new Exception("Ошибка отправки письма: $mail->ErrorInfo");
        }
    }
}