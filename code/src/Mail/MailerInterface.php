<?php

declare(strict_types=1);

namespace App\Mail;

/**
 * Контракт сервиса отправки email-сообщений.
 */
interface MailerInterface
{
    /**
     * Отправляет email-сообщение.
     *
     * @param string $to Email получателя.
     * @param string $subject Тема письма.
     * @param string $body Текст письма.
     */
    public function send(string $to, string $subject, string $body): void;
}
