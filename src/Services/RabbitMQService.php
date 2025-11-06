<?php
namespace App\Services;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQService
{
    public const ORDERS_QUEUE = 'orders.queue';
    public const NOTIFICATIONS_QUEUE = 'notifications.queue';

    public function __construct(private AMQPStreamConnection $connection)
    {
    }

    public function publish(string $queue, array $payload): void
    {
        $channel = $this->connection->channel();
        $channel->queue_declare($queue, false, true, false, false);
        $msg = new AMQPMessage(json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR), [
            'content_type' => 'application/json',
            'delivery_mode' => 2, // persistent
        ]);
        $channel->basic_publish($msg, '', $queue);
        $channel->close();
    }

    public function consume(string $queue, callable $handler): void
    {
        $channel = $this->connection->channel();
        $channel->queue_declare($queue, false, true, false, false);
        $channel->basic_qos(null, 1, null);
        $channel->basic_consume($queue, '', false, false, false, false, function ($msg) use ($handler) {
            try {
                $data = json_decode($msg->getBody(), true, 512, JSON_THROW_ON_ERROR);
                $handler($data);
                $msg->ack();
            } catch (\Throwable $e) {
                // reject and requeue=false to avoid poison pill loops
                $msg->reject(false);
                fwrite(STDERR, "[ERROR] " . $e->getMessage() . PHP_EOL);
            }
        });
        while ($channel->is_open()) {
            $channel->wait();
        }
    }
}
