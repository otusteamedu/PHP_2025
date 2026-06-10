<?php

declare(strict_types=1);

namespace MkdBot\Infrastructure\Queue;

use AMQPChannel;
use AMQPConnection;
use AMQPException;
use AMQPExchange;
use MkdBot\Domain\Enum\QueueNameType;
use MkdBot\Domain\Interface\QueuePublisherInterface;
use Psr\Log\LoggerInterface;

/**
 * Издатель сообщений в RabbitMQ — реализация QueuePublisherInterface
 * Использует PHP AMQP extension (ext-amqp)
 */
class RabbitMQPublisher implements QueuePublisherInterface
{
    private AMQPConnection $connection;

    public function __construct(
        RabbitMQConnectionFactory $connectionFactory,
        private readonly LoggerInterface $logger,
    ) {
        $this->connection = $connectionFactory->getConnection();
    }

    public function publish(QueueNameType $queue, array $message): void
    {
        try {
            $this->ensureConnection();
            $channel = new AMQPChannel($this->connection);
            $exchange = new AMQPExchange($channel);
            $exchange->setName('mkd.direct');
            $exchange->setType(AMQP_EX_TYPE_DIRECT);
            $exchange->setFlags(AMQP_DURABLE);
            $exchange->publish(
                json_encode($message, JSON_UNESCAPED_UNICODE),
                $queue->value, // routing key
                AMQP_NOPARAM,
                [
                    'content_type' => 'application/json',
                    'delivery_mode' => 2, // постоянное хранение
                ],
            );
            $this->logger->debug("Сообщение опубликовано в очередь: {$queue->value}");
        } catch (AMQPException $e) {
            $this->logger->error("Ошибка публикации в RabbitMQ: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Устанавливает соединение с RabbitMQ при необходимости
     */
    private function ensureConnection(): void
    {
        if (!$this->connection->isConnected()) {
            $this->connection->connect();
        }
    }
}
