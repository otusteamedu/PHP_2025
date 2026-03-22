<?php

declare(strict_types=1);

namespace Otus\Code\Domain\Order\Event;

use DateTimeImmutable;
use Otus\Code\Domain\Order\Enum\OrderStatus;

final class OrderEvent
{
    public function __construct(
        private readonly string $name,
        private readonly string $description,
        private readonly ?OrderStatus $status = null,
        private readonly DateTimeImmutable $occurredAt = new DateTimeImmutable(),
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getStatus(): ?OrderStatus
    {
        return $this->status;
    }

    public function getOccurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }
}
