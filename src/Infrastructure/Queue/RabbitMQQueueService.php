<?php

declare(strict_types=1);

namespace App\Infrastructure\Queue;

use App\Application\Interface\QueueServiceInterface;
use App\Infrastructure\Config\AppConfig;
use App\Infrastructure\Logging\Logger;
use Exception;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Channel\AMQPChannel;

final class RabbitMQQueueService implements QueueServiceInterface
{
    private AMQPStreamConnection $connection;
    private AMQPChannel $channel;
    private \Monolog\Logger $logger;

    public function __construct()
    {
        $config = AppConfig::getRabbitMQConfig();

        $this->connection = new AMQPStreamConnection(
            $config['host'],
            $config['port'],
            $config['user'],
            $config['password']
        );

        $this->channel = $this->connection->channel();
        $this->logger = Logger::getInstance();
    }

    /**
     * {@inheritdoc}
     */
    public function publish(string $queueName, array $message): void
    {
        $this->channel->queue_declare($queueName, false, true, false, false);

        $msg = new AMQPMessage(
            json_encode($message, JSON_THROW_ON_ERROR),
            ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]
        );

        $this->channel->basic_publish($msg, '', $queueName);
    }

    /**
     * {@inheritdoc}
     */
    public function consume(string $queueName, callable $callback): void
    {
        $this->channel->queue_declare($queueName, false, true, false, false);

        $this->logger->info("Waiting for messages in queue", ['queue' => $queueName]);

        $this->channel->basic_consume(
            $queueName,
            '',
            false,
            false,
            false,
            false,
            function (AMQPMessage $msg) use ($callback) {
                $body = json_decode($msg->getBody(), true, 512, JSON_THROW_ON_ERROR);
                $this->logger->info("Received message", ['body' => $body]);

                try {
                    $callback($body);
                    $msg->ack();
                    $this->logger->info("Message processed successfully");
                } catch (Exception $e) {
                    $this->logger->error("Error processing message", ['error' => $e->getMessage()]);
                    $msg->reject();
                }
            }
        );

        while ($this->channel->is_consuming()) {
            $this->channel->wait();
        }
    }

    /**
     * Закрывает соединение с RabbitMQ
     */
    public function __destruct()
    {
        $this->channel->close();
        $this->connection->close();
    }
}
