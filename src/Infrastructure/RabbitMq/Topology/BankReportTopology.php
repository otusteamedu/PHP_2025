<?php

declare(strict_types=1);

namespace App\Infrastructure\RabbitMq\Topology;

use App\Core\Container\Config\Contracts\DotEnvConfigInterface;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Wire\AMQPTable;

class BankReportTopology implements TopologyDefinitionInterface
{
    private readonly string $mainQueue;
    private readonly string $retryQueue;
    private readonly int $retryTtlMs;

    public function __construct(
        DotEnvConfigInterface $config,
    ) {
        $this->mainQueue = $config->get('RABBITMQ_BANK_REPORTS_QUEUE', 'bank_reports');
        $this->retryTtlMs = ((int) $config->get('RABBITMQ_BANK_REPORTS_RETRY_TTL_SECONDS', '30')) * 1000;
        $this->retryQueue = $this->mainQueue . '.retry';
    }

    public function declare(AMQPChannel $channel): void
    {
        // Main queue: bank_reports
        $channel->queue_declare(
            queue: $this->mainQueue,
            durable: true,
            auto_delete: false,
            arguments: new AMQPTable([
                'x-dead-letter-exchange' => '', // дефолтный exchange
                'x-dead-letter-routing-key' => $this->retryQueue,
            ]),
        );

        // Retry queue: bank_reports.retry
        $channel->queue_declare(
            queue: $this->retryQueue,
            durable: true,
            auto_delete: false,
            arguments: new AMQPTable([
                'x-message-ttl' => $this->retryTtlMs,
                'x-dead-letter-exchange' => '',
                'x-dead-letter-routing-key' => $this->mainQueue, // обратно в bank_reports
            ]),
        );
    }
}
