<?php

declare(strict_types=1);

namespace App\Domain\OrderNotification\ValueObject\Message;

readonly class SmsMessage
{
    public function __construct(
        private string $text,
    ) {
    }

    public function getText(): string
    {
        return $this->text;
    }
}
