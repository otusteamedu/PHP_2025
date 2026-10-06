<?php

declare(strict_types=1);

namespace App\Infrastructure\RabbitMq\Connection;

use App\Core\Container\Config\Contracts\DotEnvConfigInterface;
use App\Infrastructure\RabbitMq\Exception\AmqpConnectionException;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class AmqpConnectionFactory
{
    public function __construct(
        private readonly DotEnvConfigInterface $config,
    ) {
    }

    /**
     * @throws AmqpConnectionException
     */
    public function create(): AMQPStreamConnection
    {
        $host = $this->config->get('RABBITMQ_HOST', 'rabbitmq');
        $port = (int) $this->config->get('RABBITMQ_PUBLISHED_PORT', 5672);
        $user = $this->config->get('RABBITMQ_USER', 'guest');
        $password = $this->config->get('RABBITMQ_PASS', 'guest');

        try {
            return new AMQPStreamConnection($host, $port, $user, $password);
        } catch (\Throwable $e) {
            throw new AmqpConnectionException(
                sprintf('Failed to connect to RabbitMQ (%s:%s): %s', $host, $port, $e->getMessage()),
                previous: $e,
            );
        }
    }
}
