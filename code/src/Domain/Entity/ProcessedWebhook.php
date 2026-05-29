<?php

declare(strict_types=1);

namespace MkdBot\Domain\Entity;

use DateTimeImmutable;
use MkdBot\Domain\Enum\MessengerType;

/**
 * Сущность идемпотентности webhook — предотвращение повторной обработки
 */
class ProcessedWebhook
{
    public function __construct(
        private ?int $id = null,
        private string $messageMid = '',
        private MessengerType $messengerType = MessengerType::Max,
        private ?DateTimeImmutable $createdAt = null,
    ) {
        $this->createdAt = $createdAt ?? new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMessageMid(): string
    {
        return $this->messageMid;
    }

    public function getMessengerType(): MessengerType
    {
        return $this->messengerType;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}
