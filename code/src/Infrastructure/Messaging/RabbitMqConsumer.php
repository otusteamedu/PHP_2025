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

        echo " [*] Проверяю наличие сообщений в очереди '$queueName'...\n";

        // Пытаемся получить сообщение очереди
        $msg = $this->channel->basic_get($queueName, false);

        if ($msg instanceof AMQPMessage) {
            echo " [x] Получено сообщение.\n";
            try {
                $messageBody = json_decode($msg->body, true, 512, JSON_THROW_ON_ERROR);

                /* todo  Генерация отчета по заданным параметрам  */

                $messageBody['content'] = 'content';

                // Вызываем callback
                $onMessage($messageBody);
                // Подтверждаем успешную обработку
                $this->channel->basic_ack($msg->delivery_info['delivery_tag']);
                echo " [x] Сообщение обработано и подтверждено.\n";
            } catch (\Throwable $e) {
                echo " [!] Ошибка при обработке сообщения: " . $e->getMessage() . "\n";
                $this->channel->basic_reject($msg->delivery_info['delivery_tag'], true);
            }
        } else {
            echo " [ ] В очереди нет сообщений.\n";
        }
    }

    public function close(): void
    {
        $this->channel->close();
        $this->connection->close();
    }
}
