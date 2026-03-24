<?php

declare(strict_types=1);

namespace App\Builder;

final class EmailBuilder implements EmailInterface
{
    private ?string $senderEmail = null;
    private string $senderName = '';
    private ?string $recipientEmail = null;
    private string $recipientName = '';
    private ?string $subject = null;
    private ?string $body = null;
    /** @var array<array{path: string, name: string}> */
    private array $attachments = [];

    public function setSender(string $email, string $name = ''): void
    {
        $this->senderEmail = $email;
        $this->senderName = $name;
    }

    public function setRecipient(string $email, string $name = ''): void
    {
        $this->recipientEmail = $email;
        $this->recipientName = $name;
    }

    public function setSubject(string $subject): void
    {
        $this->subject = $subject;
    }

    public function setBody(string $body): void
    {
        $this->body = $body;
    }

    public function addAttachment(string $filePath, string $fileName = ''): void
    {
        $this->attachments[] = ['path' => $filePath, 'name' => $fileName];
    }

    public function sendEmail(string $to, string $subject, string $body): bool
    {
        // Здесь должна быть реализация отправки email с помощью выбранной библиотеки (например, PHPMailer)
        // Код для отправки email будет зависеть от используемой библиотеки и настроек сервера
        return true; // Возвращаем true для успешной отправки (для примера)
    }
}
