<?php

declare(strict_types=1);

namespace App\Application\BankStatementConsumer;

use App\Infrastructure\Messenger\Consumer\BankStatementConsumer;

final readonly class BankStatementConsumerHandler
{
    public function __construct(
        private BankStatementConsumer $bankStatementProducer,
    ) {
    }

    public function execute(): void
    {
        $this->bankStatementProducer->consume();
    }
}