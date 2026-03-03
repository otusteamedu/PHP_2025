<?php

declare(strict_types=1);

namespace App\UserInterface;

use App\Application\BankStatementConsumer\BankStatementConsumerHandler;

readonly class StartBankStatementConsumer
{
    public function __construct(
        private BankStatementConsumerHandler $bankStatementProducerHandler,
    ) {
    }

    public function execute(): void
    {
        $this->bankStatementProducerHandler->execute();
    }
}
