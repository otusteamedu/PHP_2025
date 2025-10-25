<?php

namespace Application\Queue;

use AMQPChannel;
use AMQPChannelException;
use AMQPConnection;
use AMQPConnectionException;
use AMQPEnvelopeException;
use AMQPExchange;
use AMQPExchangeException;
use AMQPQueue;
use AMQPQueueException;
use Domain\Notifier\NotifierInterface;
use Domain\Receiver\ReceiverInterface;

class RabbitMQ implements NotifierInterface, ReceiverInterface
{
    /** @var AMQPChannel|null */
    private ?AMQPChannel $channel;

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

    /**
     * @return AMQPChannel|null
     */
    public function getChannel(): ?AMQPChannel {
        return $this->channel;
    }
}