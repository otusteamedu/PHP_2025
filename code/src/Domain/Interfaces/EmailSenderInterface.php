<?php

declare(strict_types=1);

namespace App\Domain\Interfaces;

interface EmailSenderInterface
{
    /**
     * Отправляет email
     *
     * @param string $to Адрес получателя
     * @param string $subject Тема письма
     * @param string $body Тело письма (HTML)
     * @return bool Успешность отправки
     */
    public function send(string $to, string $subject, string $body): bool;
}
