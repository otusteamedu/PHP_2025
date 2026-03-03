<?php

declare(strict_types=1);

namespace App\Infrastructure\Messenger\GetBankStatement;

use AMQPChannel;
use AMQPExchange;
use App\Application\GetBankStatement\GetBankStatementRequest;
use App\Infrastructure\Messenger\AmqpConnectFactory;

final readonly class BankStatementProducer
{
    private const string EXCHANGE_NAME = 'statement';

    public function __construct(
        private AmqpConnectFactory $amqpConnectFactory,
    ) {
    }

    private function createExchange(): AMQPExchange
    {
        $conn = $this->amqpConnectFactory->create();
        $channel = new AMQPChannel($conn);

        $exchange = new AMQPExchange($channel);
        $exchange->setName(self::EXCHANGE_NAME);
        $exchange->setType('direct');
        $exchange->declareExchange();

        return $exchange;
    }

    public function produce(GetBankStatementRequest $command): void
    {
        $exchange = $this->createExchange();

        $payload = json_encode([
            'account' => $command->account,
            'dateFrom' => $command->dateFrom,
            'dateTo' => $command->dateTo,
        ], JSON_THROW_ON_ERROR);

        $exchange->publish(
            $payload,
            '',
            AMQP_NOPARAM,
        );
    }
}
