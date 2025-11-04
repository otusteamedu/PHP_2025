<?php

declare(strict_types=1);

namespace App\Application\Producer;

use App\Application\DTO\BankStatementMessage;

/**
 * Interface ProducerInterface
 * @package App\Application\Producer
 */
interface ProducerInterface
{
    /**
     * @param BankStatementMessage $message
     * @return void
     */
    public function publish(BankStatementMessage $message): void;
}
