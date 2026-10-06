<?php

declare(strict_types=1);

namespace App\Infrastructure\RabbitMq\Message;

interface MessagePayloadInterface
{
    public static function fromArray(array $data): self;

    public function toArray(): array;
}
