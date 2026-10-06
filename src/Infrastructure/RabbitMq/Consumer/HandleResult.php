<?php

declare(strict_types=1);

namespace App\Infrastructure\RabbitMq\Consumer;

readonly class HandleResult
{
    private function __construct(
        public ConsumeResult $status,
        public string $reason = '',
        public int $code = 0,
    ) {
    }

    public static function ack(int $code = 0): self
    {
        return new self(ConsumeResult::Ack, code: $code);
    }

    public static function reject(string $reason, int $code = 0): self
    {
        return new self(ConsumeResult::Reject, $reason, $code);
    }

    public static function drop(string $reason, int $code = 0): self
    {
        return new self(ConsumeResult::Drop, $reason, $code);
    }
}
