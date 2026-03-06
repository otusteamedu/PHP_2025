<?php
declare(strict_types=1);

namespace App\Infrastructure\RabbitMq\Statement;

use App\Domain\StatementRequest;
use App\Infrastructure\RabbitMq\AmqpMessageFactory;
use App\Infrastructure\RabbitMq\RabbitMqChannelProvider;
use Exception;
use JsonException;
use Psr\Log\LoggerInterface;

class StatementProducer
{
    public const string QUEUE_NAME = 'statement_requests';

    public function __construct(
        private readonly RabbitMqChannelProvider $channels,
        private readonly LoggerInterface $logger,
    ) {}

    /**
     * @param StatementRequest $request
     * @return void
     * @throws JsonException
     * @throws \Random\RandomException
     * @throws \Throwable
     */
    public function enqueue(StatementRequest $request): void
    {
        $channel = $this->channels->channel();
        $channel->queue_declare(self::QUEUE_NAME, false, true, false, false);

        $channel->basic_publish(
            AmqpMessageFactory::createMessage(json_encode($request->toArray(), JSON_THROW_ON_ERROR)),
            '',
            self::QUEUE_NAME
        );

        $this->logger->info('Enqueued statement request', ['request_id' => (string)$request->id]);
    }
}
