<?php

namespace Producer\Infrastructure\BankDetail;

use AMQPChannelException;
use AMQPConnectionException;
use AMQPExchangeException;
use AMQPQueueException;
use Producer\Application\BankDetail\BankDetailNotifierInterface;
use Producer\Application\Queue\RabbitMQ;

class RabbitMQNotifier implements BankDetailNotifierInterface
{
    private RabbitMQ $rabbitMQ;

    public function __construct(RabbitMQ $rabbitMQ) {
        $this->rabbitMQ = $rabbitMQ;
    }

    /**
     * @throws AMQPQueueException
     * @throws AMQPExchangeException
     * @throws AMQPChannelException
     * @throws AMQPConnectionException
     */
    public function run(string $message): void {
        $this->rabbitMQ->notify($message);
    }
}