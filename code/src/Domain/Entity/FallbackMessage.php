<?php

declare(strict_types=1);

namespace MkdBot\Domain\Entity;

use DateTimeImmutable;
use MkdBot\Domain\Enum\QueueNameType;

/**
 * Сущность неудачного сообщения из DLQ (Dead Letter Queue) для ручного разбора
 */
class FallbackMessage
{
    public function __construct(
        private ?int $id = null,
        private QueueNameType $queueName = QueueNameType::TelegramForward,
        private array $messageBody = [],
        private string $errorMessage = '',
        private int $xDeathCount = 0,
        private ?DateTimeImmutable $createdAt = null,
    ) {
        $this->createdAt = $createdAt ?? new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQueueName(): QueueNameType
    {
        return $this->queueName;
    }

    public function getMessageBody(): array
    {
        return $this->messageBody;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    public function getXDeathCount(): int
    {
        return $this->xDeathCount;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}
