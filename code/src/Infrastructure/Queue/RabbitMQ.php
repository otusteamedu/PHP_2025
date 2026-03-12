<?php

declare(strict_types=1);

namespace Queues\Infrastructure\Queue;

use Queues\Application\Interfaces\QueueInterface;

class RabbitMQ implements QueueInterface
{
    private ?\AMQPConnection $connection = null;
    private ?\AMQPChannel $channel = null;
    private ?\AMQPQueue $queue = null;
    private ?\AMQPExchange $exchange = null;

    public function __construct(
        private readonly string $host,
        private readonly int $port,
        private readonly string $login,
        private readonly string $password,
        private readonly string $vhost = '/',
        private readonly string $queueName = 'statements'
    ) {
    }

    public function enqueue(string $payload): void
    {
        $this->initialize();

        try {
            $this->exchange->publish(
                $payload,
                $this->queueName,
                AMQP_NOPARAM,
                [
                    'content_type' => 'application/json',
                    'delivery_mode' => 2,
                ]
            );
        } catch (\AMQPExchangeException $e) {
            throw new \RuntimeException(
                'Не удалось опубликовать сообщение: ' . $e->getMessage(),
                (int)$e->getCode(),
                $e
            );
        }
    }

    public function dequeue(): ?string
    {
        $this->initialize();

        try {
            $envelope = $this->queue->get(AMQP_AUTOACK);
            return $envelope instanceof \AMQPEnvelope ? $envelope->getBody() : null;
        } catch (\AMQPQueueException $e) {
            throw new \RuntimeException(
                'Не удалось прочитать сообщение из очереди: ' . $e->getMessage(),
                (int)$e->getCode(),
                $e
            );
        }
    }

    private function initialize(): void
    {
        if ($this->connection !== null) {
            return;
        }

        try {
            $this->createConnection();
            $this->createChannel();
            $this->declareQueue();
            $this->declareExchange();
            $this->bindQueue();
        } catch (\AMQPConnectionException $e) {
            throw new \RuntimeException(
                sprintf('Не удалось подключиться к RabbitMQ: %s:%d (%s)', $this->host, $this->port, $e->getMessage()),
                (int)$e->getCode(),
                $e
            );
        } catch (\AMQPChannelException|\AMQPQueueException|\AMQPExchangeException $e) {
            throw new \RuntimeException(
                sprintf('Ошибка инициализации RabbitMQ: %s', $e->getMessage()),
                (int)$e->getCode(),
                $e
            );
        }
    }

    private function createConnection(): void
    {
        $this->connection = new \AMQPConnection([
            'host' => $this->host,
            'port' => $this->port,
            'login' => $this->login,
            'password' => $this->password,
            'vhost' => $this->vhost,
        ]);
        $this->connection->connect();
    }

    private function createChannel(): void
    {
        $this->channel = new \AMQPChannel($this->connection);
    }

    private function declareQueue(): void
    {
        $this->queue = new \AMQPQueue($this->channel);
        $this->queue->setName($this->queueName);
        $this->queue->setFlags(AMQP_DURABLE);
        $this->queue->declareQueue();
    }

    private function declareExchange(): void
    {
        $this->exchange = new \AMQPExchange($this->channel);
        $this->exchange->setType(AMQP_EX_TYPE_DIRECT);
        $this->exchange->setName($this->queueName . '_exchange');
        $this->exchange->setFlags(AMQP_DURABLE);
        $this->exchange->declareExchange();
    }

    private function bindQueue(): void
    {
        $this->queue->bind($this->exchange->getName(), $this->queueName);
    }
}
