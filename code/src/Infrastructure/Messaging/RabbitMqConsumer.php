<?php

declare(strict_types=1);

namespace Ak\Hw\Infrastructure\Messaging;

use Ak\Hw\Domain\Messaging\QueueConsumerInterface;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMqConsumer implements QueueConsumerInterface
{
    private AMQPStreamConnection $connection;
    private \PhpAmqpLib\Channel\AMQPChannel $channel;

    public function __construct(string $host, int $port, string $user, string $password)
    {
        $this->connection = new AMQPStreamConnection($host, $port, $user, $password);
        $this->channel = $this->connection->channel();
    }

    public function consume(string $queueName, callable $onMessage): void
    {
        $this->channel->queue_declare($queueName, false, true, false, false);

        echo " [*] Checking for a message in queue '$queueName'...\n";

        // Пытаемся получить сообщение очереди
        $msg = $this->channel->basic_get($queueName, false);

        if ($msg instanceof AMQPMessage) {
            echo " [x] Received message.\n";
            try {
                $messageBody = json_decode($msg->body, true, 512, JSON_THROW_ON_ERROR);
                // Вызываем callback
                $onMessage($messageBody);
                // Подтверждаем успешную обработку
                $this->channel->basic_ack($msg->delivery_info['delivery_tag']);
                echo " [x] Message processed and acknowledged.\n";
            } catch (\Throwable $e) {
                echo " [!] Error processing message: " . $e->getMessage() . "\n";
                // Отклоняем сообщение через канал, чтобы оно вернулось в очередь (requeue = true)
                $this->channel->basic_reject($msg->delivery_info['delivery_tag'], true);
            }
        } else {
            echo " [ ] No messages in the queue.\n";
        }
    }

    public function close(): void
    {
        $this->channel->close();
        $this->connection->close();
    }
}
