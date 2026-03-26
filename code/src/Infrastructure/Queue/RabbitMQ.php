<?php

declare(strict_types=1);

namespace Api\Infrastructure\Queue;

use Api\Domain\Interfaces\QueueInterface;

final class RabbitMQ implements QueueInterface
{
    private ?\AMQPConnection $connection = null;
    private ?\AMQPChannel $channel = null;
    private ?\AMQPExchange $exchange = null;
    private ?\AMQPQueue $queue = null;

    public function __construct(
        private readonly string $host,
        private readonly int $port,
        private readonly string $login,
        private readonly string $password,
        private readonly string $vhost,
        private readonly string $queueName
    ) {
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
        $this->queue->setFlags(\AMQP_DURABLE);
        $this->queue->declareQueue();
    }

    private function declareExchange(): void
    {
        $this->exchange = new \AMQPExchange($this->channel);
        $this->exchange->setType(\AMQP_EX_TYPE_DIRECT);
        $this->exchange->setName($this->queueName . '_exchange');
        $this->exchange->declareExchange();
    }

    private function bindQueue(): void
    {
        $this->queue->bind($this->exchange->getName(), $this->queueName);
    }

    public function enqueue(array $message): void
    {
        $this->initialize();

        try {
            $body = json_encode($message, JSON_THROW_ON_ERROR);
            $this->exchange->publish(
                $body,
                $this->queue->getName(),
                \AMQP_NOPARAM,
                [
                    'content_type' => 'application/json',
                    'delivery_mode' => 2,
                ]
            );
        } catch (\JsonException $e) {
            throw new \RuntimeException('Ошибка кодирования сообщения для очереди: ' . $e->getMessage(), 0, $e);
        } catch (\AMQPExchangeException $e) {
            throw new \RuntimeException(
                'Не удалось опубликовать сообщение: ' . $e->getMessage(), (int)$e->getCode(), $e
            );
        }
    }

    public function dequeue(): ?array
    {
        $this->initialize();

        try {
            $envelope = $this->queue->get(\AMQP_AUTOACK);

            if (!$envelope instanceof \AMQPEnvelope) {
                return null;
            }

            try {
                return json_decode($envelope->getBody(), true, 512, JSON_THROW_ON_ERROR);
            } catch (\JsonException) {
                return null;
            }
        } catch (\AMQPQueueException $e) {
            throw new \RuntimeException(
                'Не удалось прочитать сообщение из очереди: ' . $e->getMessage(),
                (int)$e->getCode(),
                $e
            );
        }
    }

    public function disconnect(): void
    {
        if ($this->connection !== null && $this->connection->isConnected()) {
            $this->connection->disconnect();
        }
    }
}
