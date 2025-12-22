<?php
declare(strict_types=1);

namespace Dinargab\Homework19\Infrastructure\Service\Queue;

use Dinargab\Homework19\Domain\Queue\Repository\QueueRepositoryInterface;
use Dinargab\Homework19\Domain\Request\Entity\ReportRequest;
use PhpAmqpLib\Message\AMQPMessage;

class QueueRepository implements QueueRepositoryInterface
{

    private const QUEUE_NAME = 'queue';

    public function __construct(
        private RabbitClient $rabbitClient,
    )
    {

    }

    public function push(ReportRequest $data): void
    {
        $channel = $this->rabbitClient->getChannel();
        $message = new AMQPMessage(serialize($data));

        $channel->queue_declare(self::QUEUE_NAME, false, false, false, false);
        $channel->basic_publish($message, "",self::QUEUE_NAME);
        $this->rabbitClient->close();
    }

    public function pull(callable $messageCallable):void
    {
        $channel = $this->rabbitClient->getChannel();
        $channel->queue_declare(self::QUEUE_NAME, false, false, false, false);
        $callback = function (AMQPMessage $msg) use ($messageCallable) {
            $messageCallable(unserialize($msg->getBody()));
        };

        $channel->basic_consume(self::QUEUE_NAME, '', false, true, false, false, $callback);

        try {
            $channel->consume();
        } catch (\Throwable $exception) {
            echo $exception->getMessage();
        }


        $this->rabbitClient->close();
    }
}