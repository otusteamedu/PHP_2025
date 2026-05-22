<?php

declare(strict_types=1);

namespace App\Queue;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Throwable;

/**
 * Сервис чтения сообщений из очереди RabbitMQ
 */
final class RabbitMqConsumer
{
    private AMQPStreamConnection $connection;
    private AMQPChannel $channel;

    /**
     * @param string $host Хост RabbitMQ
     * @param int $port Порт RabbitMQ
     * @param string $user Имя пользователя RabbitMQ
     * @param string $password Пароль RabbitMQ
     * @param string $queueName Имя основной очереди
     * @param string $failedQueueName Имя очереди для ошибочных сообщений
     */
    public function __construct(
        string $host,
        int $port,
        string $user,
        string $password,
        private readonly string $queueName,
        private readonly string $failedQueueName,
    ) {
        $this->connection = new AMQPStreamConnection($host, $port, $user, $password);
        $this->channel = $this->connection->channel();
        $this->channel->queue_declare($this->queueName, false, true, false, false);
        $this->channel->queue_declare($this->failedQueueName, false, true, false, false);
    }

    /**
     * Запускает чтение сообщений из очереди
     *
     * @param callable $handler Обработчик тела сообщения
     */
    public function consume(callable $handler): void
    {
        $this->channel->basic_qos(null, 1, null);

        $this->channel->basic_consume(
            $this->queueName,
            '',
            false,
            false,
            false,
            false,
            function (AMQPMessage $message) use ($handler): void {
                try {
                    $handler($message->getBody());
                    $message->ack();
                } catch (Throwable $exception) {
                    echo 'Ошибка обработки сообщения: ' . $exception->getMessage() . PHP_EOL;

                    $this->publishToFailedQueue($message->getBody(), $exception);
                    $message->ack();
                }
            },
        );

        echo 'Worker запущен. Ожидание сообщений...' . PHP_EOL;

        while ($this->channel->is_open() && $this->channel->is_consuming()) {
            $this->channel->wait();
        }
    }

    /**
     * Публикует сообщение с ошибкой обработки в failed-очередь
     *
     * @param string $message Исходное сообщение
     * @param Throwable $exception Ошибка обработки
     */
    private function publishToFailedQueue(string $message, Throwable $exception): void
    {
        $failedMessageBody = json_encode([
            'original_message' => $message,
            'error' => $exception->getMessage(),
            'failed_at' => date('Y-m-d H:i:s'),
        ], JSON_THROW_ON_ERROR);

        $failedMessage = new AMQPMessage($failedMessageBody, [
            'content_type' => 'application/json',
            'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
        ]);

        $this->channel->basic_publish($failedMessage, '', $this->failedQueueName);
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
