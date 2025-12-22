<?php

namespace Blarkinov\RabbitMq\Service;

use Blarkinov\RabbitMq\Http\DTO\BankStatementRequestDto;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMq
{
    private static $conn = null;
    private static $channel = null;

    public function __construct()
    {
        if (self::$channel === null) {
            self::$conn = new AMQPStreamConnection(
                $_ENV['RABBITMQ_HOST'],
                $_ENV['RABBITMQ_PORT'],
                $_ENV['RABBITMQ_USER'],
                $_ENV['RABBITMQ_PASSWORD'],
            );
            self::$channel =  self::$conn->channel();
        }
    }

    public function set(string $queueName, $dto): void
    {
        self::$channel->queue_declare($queueName, false, false, false, false);

        $msg = new AMQPMessage(json_encode($dto));
        self::$channel->basic_publish($msg, '', $queueName);

        self::$channel->close();
        self::$conn->close();
    }

    public function get(string $queueName): ?BankStatementRequestDto
    {
        self::$channel->queue_declare($queueName, false, false, false, false);
        $message = self::$channel->basic_get($queueName, true);

        $body = $message instanceof AMQPMessage
            ? $message->getBody()
            : null;

        self::$channel->close();

        if ($body) {
            $data = json_decode($body, true);

            return new BankStatementRequestDto($data['dateFrom'], $data['dateTo'], $data['transactionType']);
        }

        return null;
    }
}
