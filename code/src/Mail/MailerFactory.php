<?php

declare(strict_types=1);

namespace App\Mail;

/**
 * Фабрика сервиса отправки email.
 */
final class MailerFactory
{
    /**
     * Создает сервис отправки email по SMTP-настройкам окружения.
     *
     * @return MailerInterface
     */
    public function create(): MailerInterface
    {
        return new PhpMailerMailer(
            getenv('SMTP_HOST') ?: 'mailpit',
            (int) (getenv('SMTP_PORT') ?: '1025'),
            getenv('SMTP_USERNAME') ?: '',
            getenv('SMTP_PASSWORD') ?: '',
            getenv('SMTP_ENCRYPTION') ?: '',
            getenv('SMTP_FROM_EMAIL') ?: 'no-reply@example.local',
            getenv('SMTP_FROM_NAME') ?: 'Statement App',
        );
    }
}
