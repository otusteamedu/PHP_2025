<?php

declare(strict_types=1);

namespace Otus\Queue\Infrastructure\Queue\Adapter;

use ErrorException;
use Exception;
use Otus\Queue\Infrastructure\Queue\Payload;
use PhpAmqpLib\Channel\AbstractChannel;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

final readonly class AmqpAdapter implements AdapterInterface
{
    /**
     * @var AMQPStreamConnection
     */
    private AMQPStreamConnection $connection;

    /**
     * @var AbstractChannel|AMQPChannel
     */
    private AMQPChannel|AbstractChannel $channel;

    /**
     * @param array $connection
     * @param array $queue
     * @param array $exchange
     * @param array $consume
     *
     * @throws Exception
     */
    public function __construct(
        array $connection,
        private array $queue,
        private array $exchange,
        private array $consume,
    ) {
        $this->connection = new AMQPStreamConnection(...$connection);

        $this->channel = $this->connection->channel();
    }

    /**
     * @throws Exception
     */
    public function __destruct()
    {
        $this->channel->close();
        $this->connection->close();
    }

    /**
     * @param Payload $payload
     */
    public function push(Payload $payload): void
    {
        $message = new AMQPMessage($payload->get('message'));

        $this->channel->queue_declare($payload->get('queue'), ...$this->queue);
        $this->channel->exchange_declare($payload->get('exchange'), ...$this->exchange);

        $this->channel->basic_publish($message, $payload->get('exchange'));
    }

    /**
     * @throws ErrorException
     */
    public function pull(Payload $payload): void
    {
        $this->channel->queue_declare($payload->get('queue'), ...$this->queue);
        $this->channel->exchange_declare($payload->get('exchange'), ...$this->exchange);

        $this->channel->queue_bind($payload->get('queue'), $payload->get('exchange'));

        $this->channel->basic_consume(
            $payload->get('queue'),
            ...$this->consume,
        );

        $this->channel->consume();
    }

    /**
     * @return bool
     */
    public function ping(): bool
    {
        return $this->connection->isConnected();
    }
}
