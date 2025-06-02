<?php

declare(strict_types=1);

namespace App\Infrastructure\Rabbit;

use ErrorException;
use Exception;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * Class Client
 * @package App\Infrastructure\Rabbit
 */
class Client
{
    /**
     * @var AMQPChannel
     */
    private AMQPChannel $channel;
    /**
     * @var string
     */
    private string $queueName;

    /**
     * @throws Exception
     */
    public function __construct(Config $config)
    {
        try {
            $connection = new AMQPStreamConnection(
                $config->getHost(),
                $config->getPort(),
                $config->getUser(),
                $config->getPassword(),
            );

            $this->channel = $connection->channel();
            $this->queueName = $config->getQueueName();

            $this->channel->queue_declare(
                $this->queueName,
                false,
                false,
                false,
                false
            );
        } catch (Exception $e) {
            throw new Exception('Error connecting to rabbit: ' . $e->getMessage());
        }
    }

    /**
     * @param string $message
     * @return void
     */
    public function publish(string $message): void
    {
        $amqpMessage = new AMQPMessage($message);
        $this->channel->basic_publish($amqpMessage, '', $this->queueName);
    }

    /**
     * @throws ErrorException
     */
    public function consume(callable $callback): void
    {
        $this->channel->basic_consume(
            $this->queueName,
            '',
            false,
            true,
            false,
            false,
            $callback
        );

        $this->channel->consume();
    }
}
