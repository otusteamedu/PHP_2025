<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\DataMapper;

use App\Core\Database\DataMapper\AbstractMapper;
use App\Core\Database\DataMapper\IdentityVoMapperInterface;
use App\Domain\OrderNotification\Entity\NotificationTask;
use App\Domain\OrderNotification\ValueObject\Order\OrderEvent;
use App\Domain\OrderNotification\ValueObject\Order\OrderId;
use App\Domain\OrderNotification\ValueObject\Recipient\Email;
use App\Domain\OrderNotification\ValueObject\Recipient\Phone;
use App\Domain\OrderNotification\ValueObject\Task\NotificationChannel;
use App\Domain\OrderNotification\ValueObject\Task\TaskId;
use App\Domain\OrderNotification\ValueObject\Task\TaskStatus;

class NotificationTaskMapper extends AbstractMapper implements IdentityVoMapperInterface
{
    public function createIdentity(int $id): TaskId
    {
        return TaskId::fromInt($id);
    }

    public function mapRowToEntity(array $row): NotificationTask
    {
        $channel = NotificationChannel::from($row['notification_channel']);
        $recipient = match ($channel) {
            NotificationChannel::Email => new Email($row['recipient']),
            NotificationChannel::Sms => new Phone($row['recipient']),
        };

        $task = new NotificationTask(
            orderId: OrderId::fromInt($row['order_id']),
            orderEvent: OrderEvent::from($row['order_event']),
            channel: $channel,
            recipient: $recipient,
            status: TaskStatus::from($row['task_status']),
            createdAt: new \DateTimeImmutable($row['created_at']),
            processedAt: ($row['processed_at'] !== null) ? new \DateTimeImmutable($row['processed_at']) : null,
            failureReason: $row['failure_reason'],
        );

        $this->hydrateId($task, $row['id']);
        $this->hydrate($task, ['attempts' => $row['attempts']]);

        return $task;
    }

    public function mapEntityToRow(object $entity): array
    {
        if (!$entity instanceof NotificationTask) {
            throw new \InvalidArgumentException(sprintf(
                '[%s] expects [%s] entity, got [%s]',
                NotificationTaskMapper::class,
                NotificationTask::class,
                get_class($entity),
            ));
        }

        return [
            'id' => $entity->getId()?->value(),
            'order_id' => $entity->getOrderId()->value(),
            'order_event' => $entity->getOrderEvent()->value,
            'task_status' => $entity->getStatus()->value,
            'notification_channel' => $entity->getChannel()->value,
            'recipient' => $entity->getRecipient()->value(),
            'failure_reason' => $entity->getFailureReason(),
            'attempts' => $entity->getAttempts(),
            'created_at' => $entity->getCreatedAt()->format('Y-m-d H:i:s'),
            'processed_at' => $entity->getProcessedAt()?->format('Y-m-d H:i:s'),
        ];
    }
}
