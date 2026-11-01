<?php

declare(strict_types=1);

namespace App\Infrastructure\RabbitMq\Publisher;

use App\Domain\OrderNotification\Contract\NotificationTaskPublisherInterface;
use App\Domain\OrderNotification\Entity\NotificationTask;
use App\Domain\OrderNotification\Exception\Task\NotificationTaskPublishingException;
use App\Infrastructure\RabbitMq\Config\NotificationAmqpConfig;
use App\Infrastructure\RabbitMq\Exception\AmqpConnectionException;
use App\Infrastructure\RabbitMq\Message\NotificationTaskMessage;
use App\Infrastructure\RabbitMq\Producer\RabbitMqProducer;

class NotificationTaskPublisher implements NotificationTaskPublisherInterface
{
    public function __construct(
        private readonly RabbitMqProducer $producer,
        private readonly NotificationAmqpConfig $config,
    ) {
    }

    public function publish(NotificationTask $task): void
    {
        try {
            $message = new NotificationTaskMessage($task->getId()->value());
            $body = json_encode($message->toArray(), JSON_THROW_ON_ERROR);

            $this->producer->publish(
                body: $body,
                exchange: $this->config->exchange(),
                routingKey: $this->config->routingKey($task->getChannel()),
            );
        } catch (AmqpConnectionException | \JsonException $e) {
            throw NotificationTaskPublishingException::failed($e);
        }
    }
}
