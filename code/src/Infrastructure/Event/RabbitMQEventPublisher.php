<?php

declare(strict_types=1);

namespace App\Infrastructure\Event;

use App\Domain\Event\EventPublisherInterface;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQEventPublisher implements EventPublisherInterface
{
    private \PhpAmqpLib\Channel\AMQPChannel $channel;
    private string $queueName;

    public function __construct(AMQPStreamConnection $connection, string $queueName)
    {
        $this->channel = $connection->channel();
        $this->queueName = $queueName;

        // Объявляем очередь, если она еще не существует
        $this->channel->queue_declare(
            $this->queueName,
            false, // passive
            true,  // durable
            false, // exclusive
            false  // auto_delete
        );
    }

    public function publish(object $event): void
    {
        $data = json_encode($event); // Сериализуем событие в JSON

        $msg = new AMQPMessage(
            $data,
            ['content_type' => 'application/json', 'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]
        );

        $this->channel->basic_publish($msg, '', $this->queueName);
    }
}
