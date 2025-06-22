<?php

declare(strict_types=1);

namespace App\Infrastructure\RabbitMQ;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;

final class RabbitMQChannel
{
    private RabbitMQConnection $rabbitMQConnection;

    private array $options;

    private const DEFAULT_QUEUE_NAME = 'messages';

    private const EXCHANGE = '';

    public function __construct(RabbitMQConnection $rabbitMQConnection)
    {
        $this->rabbitMQConnection = $rabbitMQConnection;
    }

    public function setQueue(string $value = self::DEFAULT_QUEUE_NAME): self
    {
        $this->options['queue'] = $value;

        return $this;
    }

    public function getQueue(): string
    {
        return $this->options['queue'] ?? self::DEFAULT_QUEUE_NAME;
    }

    public function setPassive(bool $value = false): self
    {
        $this->options['passive'] = $value;

        return $this;
    }

    public function setDurable(bool $value = false): self
    {
        $this->options['durable'] = $value;

        return $this;
    }

    public function setExclusive(bool $value = false): self
    {
        $this->options['exclusive'] = $value;

        return $this;
    }

    public function setAutoDelete(bool $value = false): self
    {
        $this->options['auto_delete'] = $value;

        return $this;
    }

    public function setExchange(string $value = self::EXCHANGE): self
    {
        $this->options['exchange'] = $value;

        return $this;
    }

    public function getChannel(): AMQPChannel
    {
        $channel = $this->getAMQPStreamConnection()->channel();
        $options = $this->getOptions();

        $channel->queue_declare(
            $options['queue'],
            $options['passive'],
            $options['durable'],
            $options['exclusive'],
            $options['auto_delete']
        );

        return $channel;
    }

    public function getAMQPStreamConnection(): AMQPStreamConnection
    {
        return $this->rabbitMQConnection->getConnection();
    }

    public function getOptions(): array
    {
        $this->setDefaultOptions();;

        return $this->options;
    }

    private function setDefaultOptions(): void
    {
        if (!isset($this->options['queue'])) {
            $this->setQueue();
        }

        if (!isset($this->options['passive'])) {
            $this->setPassive();
        }

        if (!isset($this->options['durable'])) {
            $this->setDurable();
        }

        if (!isset($this->options['exclusive'])) {
            $this->setExclusive();
        }

        if (!isset($this->options['auto_delete'])) {
            $this->setAutoDelete();
        }

        if (!isset($this->options['exchange'])) {
            $this->setExchange();
        }
    }
}
