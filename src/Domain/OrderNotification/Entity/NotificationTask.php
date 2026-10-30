<?php

declare(strict_types=1);

namespace App\Domain\OrderNotification\Entity;

use App\Domain\OrderNotification\ValueObject\Order\OrderEvent;
use App\Domain\OrderNotification\ValueObject\Order\OrderId;
use App\Domain\OrderNotification\ValueObject\Recipient\RecipientInterface;
use App\Domain\OrderNotification\ValueObject\Task\NotificationChannel;
use App\Domain\OrderNotification\ValueObject\Task\TaskId;
use App\Domain\OrderNotification\ValueObject\Task\TaskStatus;

class NotificationTask
{
    private ?TaskId $id = null;

    public function __construct(
        private readonly OrderId $orderId,
        private readonly OrderEvent $orderEvent,
        private readonly NotificationChannel $channel,
        private readonly RecipientInterface $recipient,
        private TaskStatus $status = TaskStatus::Pending,
        private readonly \DateTimeImmutable $createdAt = new \DateTimeImmutable(),
        private ?\DateTimeImmutable $processedAt = null,
    ) {
    }

    public function getId(): ?TaskId { return $this->id; }
    public function getOrderId(): OrderId { return $this->orderId; }
    public function getOrderEvent(): OrderEvent { return $this->orderEvent; }
    public function getChannel(): NotificationChannel { return $this->channel; }
    public function getRecipient(): RecipientInterface { return $this->recipient; }
    public function getStatus(): TaskStatus { return $this->status; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function getProcessedAt(): ?\DateTimeImmutable { return $this->processedAt; }

    public static function create(
        OrderId $orderId,
        OrderEvent $orderEvent,
        NotificationChannel $channel,
        RecipientInterface $recipient,
    ): self {
        return new self($orderId, $orderEvent, $channel, $recipient);
    }
}
