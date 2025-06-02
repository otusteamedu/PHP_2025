<?php

namespace App\Classes;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\Dotenv\Dotenv;

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

    private function declareExchange(AMQPChannel $channel, string $name, string $type)
    {
        return $channel->exchange_declare($name, $type, false, false, false);
    }

}