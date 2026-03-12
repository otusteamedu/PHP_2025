<?php

declare(strict_types=1);

namespace App\Infrastructure\Transport\Amqp;

use AMQPChannel;
use AMQPConnection;
use AMQPExchange;
use AMQPQueue;
use RuntimeException;

final readonly class RequestProducer
{
    private const string EXCHANGE_NAME = 'request';
    private const string QUEUE_NAME = 'request';

    private AMQPConnection $conn;
    private AMQPChannel $channel;

    public function __construct(
        private AmqpConnectFactory $connectFactory,
    ) {
        $this->conn = $this->connectFactory->create();
        $this->channel = new AMQPChannel($this->conn);
    }

    private function createExchange(): AMQPExchange
    {
        $exchange = new AMQPExchange($this->channel);
        $exchange->setName(self::EXCHANGE_NAME);
        $exchange->setType(AMQP_EX_TYPE_DIRECT);
        $exchange->declareExchange();

        return $exchange;
    }

    private function createQueue(): AMQPQueue
    {
        $queue = new AMQPQueue($this->channel);
        $queue->setName(self::QUEUE_NAME);
        $queue->setFlags(AMQP_DURABLE);
        $queue->declareQueue();
        $queue->bind(self::EXCHANGE_NAME);

        return $queue;
    }

    public function publish(int $requestId, string $payload): void
    {
        $exchange = $this->createExchange();
        $this->createQueue();

        $message = json_encode([
            'request_id' => $requestId,
            'payload' => $payload,
        ], JSON_THROW_ON_ERROR);

        $exchange->publish($message, '', AMQP_NOPARAM);
    }
}
