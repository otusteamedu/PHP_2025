<?php

declare(strict_types=1);

namespace App\Infrastructure\RabbitMq\Topology;

use App\Domain\OrderNotification\ValueObject\Task\NotificationChannel;
use App\Infrastructure\RabbitMq\Config\NotificationAmqpConfig;
use App\Infrastructure\RabbitMq\Producer\ExchangeType;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Wire\AMQPTable;

class OrderNotificationTopology implements TopologyDefinitionInterface
{
    public function __construct(
        private readonly NotificationAmqpConfig $config,
    ) {
    }

    public function declare(AMQPChannel $channel): void
    {
        $exchange = $this->config->exchange();

        $channel->exchange_declare(
            exchange: $exchange,
            type: ExchangeType::Direct->value,
            durable: true,
            auto_delete: false,
        );

        $this->declareChannelChain(
            channel: $channel,
            exchange: $exchange,
            queueName: $this->config->emailQueue(),
            routingKey: $this->config->routingKey(NotificationChannel::Email),
            retryTtlMs: $this->config->retryTtlMs(NotificationChannel::Email),
        );

        $this->declareChannelChain(
            channel: $channel,
            exchange: $exchange,
            queueName: $this->config->smsQueue(),
            routingKey: $this->config->routingKey(NotificationChannel::Sms),
            retryTtlMs: $this->config->retryTtlMs(NotificationChannel::Sms),
        );
    }

    private function declareChannelChain(
        AMQPChannel $channel,
        string $exchange,
        string $queueName,
        string $routingKey,
        int $retryTtlMs,
    ): void {
        $retryQueueName = $queueName . '.retry';

        $channel->queue_declare(
            queue: $queueName,
            durable: true,
            auto_delete: false,
            arguments: new AMQPTable([
                'x-dead-letter-exchange' => '', // дефолтный exchange
                'x-dead-letter-routing-key' => $retryQueueName, // в retry-очередь
            ]),
        );

        $channel->queue_declare(
            queue: $retryQueueName,
            durable: true,
            auto_delete: false,
            arguments: new AMQPTable([
                'x-message-ttl' => $retryTtlMs,
                'x-dead-letter-exchange' => '',
                'x-dead-letter-routing-key' => $queueName, // обратно в основную
            ]),
        );

        $channel->queue_bind($queueName, $exchange, $routingKey);
    }
}
