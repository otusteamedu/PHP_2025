<?php

namespace App\Infrastructure\Queue;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitmqSender
{
    private $host;
    private $port;
    private $username;
    private $password;

    public function __construct()
    {
        $this->host = getenv('RABBITMQ_HOST');
        $this->port = getenv('RABBITMQ_PORT');
        $this->username = getenv('RABBITMQ_USERNAME');
        $this->password = getenv('RABBITMQ_PASSWORD');
    }

    public function sendMessage($message): void
    {
        $connection = new AMQPStreamConnection($this->host, $this->port, $this->username, $this->password);
        $channel = $connection->channel();

        $channel->queue_declare('todos', false, false, false, false);

        $msg = new AMQPMessage($message);
        $channel->basic_publish($msg, '', 'todos');

        $channel->close();
        $connection->close();
    }
}