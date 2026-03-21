<?php

declare(strict_types=1);

namespace Ak\Hw\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailService
{
    private PHPMailer $mailer;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);
        $this->mailer->CharSet = 'UTF-8';
        $this->mailer->Encoding = 'base64';

        // Настройки из переменных окружения
        $this->mailer->isSMTP();
        $this->mailer->Host = $_ENV['MG_HOST'] ?? 'mailhog';
        $this->mailer->Port = (int)($_ENV['MG_PORT'] ?? 1025);
        $this->mailer->SMTPAuth = filter_var($_ENV['MG_SMTP_AUTH'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $this->mailer->SMTPSecure = $_ENV['MG_SMTP_SECURE'] ?? false;
    }

    public function send(string $to, string $subject, string $body): bool
    {
        try {
            // Получатели и отправитель
            $fromEmail = $_ENV['MG_EMAIL_FROM'] ?? 'noreply@bestapp.com';
            $fromName = $_ENV['MG_MAILER_NAME'] ?? 'Отправитель по умолчанию';
            $this->mailer->setFrom($fromEmail, $fromName);
            $this->mailer->addAddress($to);

            // Контент
            $this->mailer->isHTML(true);
            $this->mailer->Subject = $subject;
            $this->mailer->Body    = $body;
            $this->mailer->AltBody = strip_tags($body);

            $this->mailer->send();
            return true;
        } catch (Exception $e) {
            // Логируем ошибку на русском
            error_log("Не удалось отправить сообщение. Ошибка Mailer: {$this->mailer->ErrorInfo}");
            return false;
        }
    }
}
