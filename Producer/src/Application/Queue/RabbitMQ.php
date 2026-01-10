<?php

namespace Producer\Application\Queue;

use AMQPChannel;
use AMQPChannelException;
use AMQPConnection;
use AMQPConnectionException;
use AMQPExchange;
use AMQPExchangeException;
use AMQPQueue;
use AMQPQueueException;
use Producer\Domain\Notifier\NotifierInterface;

class RabbitMQ implements NotifierInterface
{
    /** @var AMQPChannel */
    private AMQPChannel $channel;

    /**
     * @throws AMQPConnectionException
     */
    public function __construct() {
        $connection = new AMQPConnection([
            'host' => getenv('RABBITMQ_HOST'),
            'port' => 5672,
            'login' => getenv('RABBITMQ_DEFAULT_USER'),
            'password' => getenv('RABBITMQ_DEFAULT_PASS'),
            'vhost' => '/'
        ]);

        $connection->connect();

        $this->channel = new AMQPChannel($connection);
    }

    /**
     * @throws AMQPQueueException
     * @throws AMQPExchangeException
     * @throws AMQPConnectionException
     * @throws AMQPChannelException
     */
    public function notify(string $message): void {
        $exchange = new AMQPExchange($this->channel);
        $exchange->setName('hash_exchange');
        $exchange->setType('x-consistent-hash');
        $exchange->setArgument('hash-header', 'hash-on');
        $exchange->declareExchange();

        $queue = new AMQPQueue($this->channel);
        $queue->setName('queue_1');
        $queue->declareQueue();
        $queue->bind('hash_exchange', '1');

        $number = rand(1, 100);

        $exchange->publish($message, '', AMQP_NOPARAM, [
            'headers' => ['hash-on' => "user$number"]
        ]);
    }
}