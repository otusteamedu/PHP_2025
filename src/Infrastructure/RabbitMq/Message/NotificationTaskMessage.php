<?php

declare(strict_types=1);

namespace App\Infrastructure\RabbitMq\Message;

readonly class NotificationTaskMessage implements MessagePayloadInterface
{
    public function __construct(
        public int $taskId,
    ) {
    }

    public static function fromArray(array $data): self
    {
        $taskId = $data['taskId'] ?? null;

        if (!is_int($taskId) || $taskId <= 0) {
            throw new \InvalidArgumentException('Empty or invalid taskId in the message.');
        }

        return new self($taskId);
    }

    public function toArray(): array
    {
        return [
            'taskId' => $this->taskId,
        ];
    }
}
