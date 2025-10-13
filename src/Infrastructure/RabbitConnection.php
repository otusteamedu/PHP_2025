<?php
namespace App\Infrastructure;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Channel\AMQPChannel;

class RabbitConnection
{
    private string $host;
    private int $port;
    private string $user;
    private string $pass;
    private string $vhost;
    private string $queue;

    public function __construct(string $host, int $port, string $user, string $pass, string $vhost, string $queue)
    {
        $this->host = $host;
        $this->port = $port;
        $this->user = $user;
        $this->pass = $pass;
        $this->vhost = $vhost;
        $this->queue = $queue;
    }

    public function getQueueName(): string
    {
        return $this->queue;
    }

    /**
     * @throws \Exception
     */
    public function channel(): array
    {
        $connection = new AMQPStreamConnection($this->host, $this->port, $this->user, $this->pass, $this->vhost);
        $channel = $connection->channel();
        $channel->queue_declare($this->queue, false, true, false, false);
        return [$connection, $channel];
    }
}
