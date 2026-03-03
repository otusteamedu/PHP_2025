<?php

declare(strict_types=1);

namespace App\Application\GetBankStatement;

use App\Infrastructure\Messenger\GetBankStatement\BankStatementProducer;

readonly class GetBankStatementHandler
{
    public function __construct(
        public BankStatementProducer $producer,
    ) {
    }

    public function execute(GetBankStatementRequest $command): void
    {
        $this->producer->produce($command);
    }
}
