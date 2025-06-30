<?php

declare(strict_types=1);

namespace App\Infrastructure\RabbitMQ;

use Exception;
use PhpAmqpLib\Connection\AMQPStreamConnection;

final class RabbitMQConnection
{
    private AMQPStreamConnection $connection;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $projectName = \getenv('PROJECT_NAME');
        $host = $projectName . '_' . \getenv('RABBITMQ_CONTAINER_NAME');
        $port = \getenv('RABBITMQ_PORT');
        $user = \getenv('RABBITMQ_DEFAULT_PASS');
        $password = \getenv('RABBITMQ_DEFAULT_USER');

        try {
            $this->connection = new AMQPStreamConnection($host, $port, $user, $password);
        } catch (\Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function getConnection(): AMQPStreamConnection
    {
        return $this->connection;
    }
}
