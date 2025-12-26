<?php
declare(strict_types=1);

namespace Dinargab\Homework20\Infrastructure\Services;

use Dinargab\Homework20\Domain\Job\Entity\Job;
use Dinargab\Homework20\Domain\Queue\QueueServiceInterface;
use Dinargab\Homework20\Infrastructure\Client\RabbitMQClient;
use PhpAmqpLib\Message\AMQPMessage;

class QueueService implements QueueServiceInterface
{
    private const QUEUE_NAME = 'queue';
    
    public function __construct(
        private readonly RabbitMQClient $connection,
    )
    {
        
    }

    public function push(Job $job): void
    {
        $channel = $this->connection->getChannel();
        $message = new AMQPMessage(serialize($job));

        $channel->queue_declare(self::QUEUE_NAME, false, false, false, false);
        $channel->basic_publish($message, "",self::QUEUE_NAME);
        $this->connection->close();

    }

    public function pull(callable $callback): \Generator
    {
        $channel = $this->connection->getChannel();
        $channel->queue_declare(self::QUEUE_NAME, false, false, false, false);

        while (true) {
            $message = $channel->basic_get(self::QUEUE_NAME, true);

            if ($message instanceof AMQPMessage) {
                $job = unserialize($message->getBody());
                sleep(10);
                yield $job;
            } else {
                yield null;
                usleep(100000); // 100ms
            }
        }
    }
}