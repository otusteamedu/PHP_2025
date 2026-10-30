<?php

declare(strict_types=1);

namespace App\Domain\OrderNotification\ValueObject\Message;

readonly class EmailMessage
{
    public function __construct(
        private string $subject,
        private string $body,
    ) {
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getBody(): string
    {
        return $this->body;
    }
}
