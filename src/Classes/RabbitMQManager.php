<?php

namespace App\Classes;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;


class RabbitMQManager
{
    const EXCHANGE_NAME = 'hash_exchange';
    const EXCHANGE_TYPE = 'x-consistent-hash';


    private AMQPStreamConnection $connection;
    private AMQPChannel $channel;

    public function __construct(){
        $this->connection = new AMQPStreamConnection(
            $_ENV['RABBIT_MQ_HOST'],
            $_ENV['RABBIT_MQ_PORT'],
            $_ENV['RABBIT_MQ_USER'],
            $_ENV['RABBIT_MQ_PASSWORD'],
            $_ENV['RABBIT_MQ_V_HOST']
        );

        $this->channel = $this->connection->channel();
    }

    public function pushMessage(string $message)
    {
        $this->declareExchange($this->channel, self::EXCHANGE_NAME, self::EXCHANGE_TYPE);
        $msg = new AMQPMessage($message);
        $this->channel->basic_publish($msg, self::EXCHANGE_NAME);
    }

    public function consumeMessages()
    {
        $this->channel->exchange_declare(self::EXCHANGE_NAME, self::EXCHANGE_TYPE, false, false, false);
        list($queue_name, ,) = $this->channel->queue_declare("", false, false, true, false);

        $this->channel->queue_bind($queue_name, 'logs');
        $callback = function (AMQPMessage $msg) {
            echo ' [x] ', $msg->getBody(), "\n";
        };

        $this->channel->basic_consume($queue_name, '', false, true, false, false, $callback);

        try {
            $this->channel->consume();
        } catch (\Throwable $exception) {
            echo $exception->getMessage();
        }

        $this->channel->close();
        $this->connection->close();
    }

    private function declareExchange(AMQPChannel $channel, string $name, string $type)
    {
        return $channel->exchange_declare($name, $type, false, false, false);
    }

}