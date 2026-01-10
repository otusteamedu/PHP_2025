<?php

namespace Consumer\Application\Queue;

use AMQPChannel;
use AMQPChannelException;
use AMQPConnection;
use AMQPConnectionException;
use AMQPEnvelopeException;
use AMQPExchange;
use AMQPExchangeException;
use AMQPQueue;
use AMQPQueueException;
use Consumer\Domain\Receiver\ReceiverInterface;

class RabbitMQ implements ReceiverInterface
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
     * @param callable $callback
     * @return void
     * @throws AMQPChannelException
     * @throws AMQPConnectionException
     * @throws AMQPExchangeException
     * @throws AMQPQueueException
     * @throws AMQPEnvelopeException
     */
    public function receive(callable $callback): void {
        $channel = $this->channel;
        $channel->qos(0, 1);

        $exchange = new AMQPExchange($channel);
        $exchange->setName('hash_exchange');
        $exchange->setType('x-consistent-hash');
        $exchange->setArgument('hash-header', 'hash-on');
        $exchange->declareExchange();

        $queue = new AMQPQueue($channel);
        $queue->setName('queue_1');
        $queue->declareQueue();
        $queue->bind('hash_exchange', '1'); // вес очереди

        echo "Прослушка сообщений...\n";

        $queue->consume($callback, AMQP_NOPARAM);
    }
}