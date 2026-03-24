<?php

declare(strict_types=1);

namespace App\Builder;

interface EmailInterface
{
    public function setSender(string $email, string $name = ''): void;
    public function setRecipient(string $email, string $name = ''): void;
    public function setSubject(string $subject): void;
    public function setBody(string $body): void;
    public function addAttachment(string $filePath, string $fileName = ''): void;
    public function sendEmail(string $to, string $subject, string $body): bool;
}
