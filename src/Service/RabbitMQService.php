<?php

namespace App\Service;

use App\Message\StatementRequestMessage;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQService implements QueueServiceInterface
{
    private AMQPStreamConnection $connection;
    private string $queueName;

    public function __construct(
        string $host = 'rabbitmq',
        int $port = 5672,
        string $user = 'guest',
        string $password = 'guest',
        string $queueName = 'statement_requests'
    ) {
        $this->connection = new AMQPStreamConnection($host, $port, $user, $password);
        $this->queueName = $queueName;
        $this->declareQueue();
    }

    private function declareQueue(): void
    {
        $channel = $this->connection->channel();
        $channel->queue_declare($this->queueName, false, true, false, false);
        $channel->close();
    }

    public function sendMessage(StatementRequestMessage $message): void
    {
        $channel = $this->connection->channel();
        
        $msg = new AMQPMessage(
            json_encode($message->toArray()),
            ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]
        );
        
        $channel->basic_publish($msg, '', $this->queueName);
        $channel->close();
    }

    public function consume(callable $callback): void
    {
        $channel = $this->connection->channel();
        
        echo " [*] Waiting for messages. To exit press CTRL+C\n";

        $channel->basic_consume(
            $this->queueName,
            '',
            false,
            true,
            false,
            false,
            function (AMQPMessage $msg) use ($callback) {
                $data = json_decode($msg->body, true);
                $message = StatementRequestMessage::fromArray($data);
                $callback($message);
            }
        );

        while ($channel->is_consuming()) {
            $channel->wait();
        }

        $channel->close();
    }

    public function __destruct()
    {
        if (isset($this->connection)) {
            $this->connection->close();
        }
    }
}