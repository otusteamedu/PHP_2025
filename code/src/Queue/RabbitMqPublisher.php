<?php

declare(strict_types=1);

namespace App\Queue;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * Сервис отправки сообщений в очередь RabbitMQ.
 */
final class RabbitMqPublisher implements QueuePublisherInterface
{
    private AMQPStreamConnection $connection;
    private AMQPChannel $channel;

    /**
     * @param string $host Хост RabbitMQ.
     * @param int $port Порт RabbitMQ.
     * @param string $user Имя пользователя RabbitMQ.
     * @param string $password Пароль RabbitMQ.
     * @param string $queueName Имя очереди для публикации сообщений.
     */
    public function __construct(
        string $host,
        int $port,
        string $user,
        string $password,
        private readonly string $queueName,
    ) {
        $this->connection = new AMQPStreamConnection($host, $port, $user, $password);
        $this->channel = $this->connection->channel();
        $this->channel->queue_declare($this->queueName, false, true, false, false);
    }

    /**
     * Публикует сообщение в очередь RabbitMQ.
     *
     * @param string $message Тело сообщения.
     */
    public function publish(string $message): void
    {
        $rabbitMessage = new AMQPMessage($message, [
            'content_type' => 'application/json',
            'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
        ]);

        $this->channel->basic_publish($rabbitMessage, '', $this->queueName);
    }

    public function __destruct()
    {
        if (isset($this->channel) && $this->channel->is_open()) {
            $this->channel->close();
        }

        if (isset($this->connection) && $this->connection->isConnected()) {
            $this->connection->close();
        }
    }
}
