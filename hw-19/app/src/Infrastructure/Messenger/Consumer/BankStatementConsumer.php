<?php

declare(strict_types=1);

namespace App\Infrastructure\Messenger\Consumer;

use AMQPChannel;
use AMQPEnvelope;
use AMQPExchange;
use AMQPQueue;
use App\Infrastructure\Messenger\AmqpConnectFactory;
use DateTimeImmutable;

final readonly class BankStatementConsumer
{
    private const string QUEUE_NAME = 'statement';
    private const string EXCHANGE_NAME = 'statement';

    public function __construct(
        private AmqpConnectFactory $amqpConnectFactory,
    )
    {
    }

    private function createQueue(): AMQPQueue
    {
        $conn = $this->amqpConnectFactory->create();
        $channel = new AMQPChannel($conn);

        $exchange = new AMQPExchange($channel);
        $exchange->setName(self::EXCHANGE_NAME);
        $exchange->setType('direct');
        $exchange->declareExchange();

        $queue = new AMQPQueue($channel);
        $queue->setName(self::QUEUE_NAME);
        $queue->setFlags(AMQP_DURABLE);
        $queue->declareQueue();
        $queue->bind(self::EXCHANGE_NAME);

        return $queue;
    }

    public function consume(): void
    {
        $queue = $this->createQueue();

        $queue->consume(function (AMQPEnvelope $envelope, AMQPQueue $queue) {
            $data = json_decode($envelope->getBody(), true);

            $account = $data['account'];
            $dateFrom = new DateTimeImmutable($data['dateFrom']['date']);
            $dateTo = new DateTimeImmutable($data['dateTo']['date']);

            echo "Получено сообщение:\n";
            echo "🗂️ Счет: $account\n";
            echo "📅 Период с: " . $dateFrom->format('Y-m-d') . " по: " . $dateTo->format('Y-m-d') . "\n";

            $queue->ack($envelope->getDeliveryTag(), AMQP_REQUEUE);
        });
    }
}
