<?php

declare(strict_types=1);

namespace App\Application\UseCase\SendBankStatementRequest;

use App\Application\DTO\BankStatementMessage;
use App\Application\Producer\ProducerInterface;

/**
 * Class SendBankStatementRequestUseCase
 * @package App\Application\UseCase\SendBankStatementRequest
 */
readonly class SendBankStatementRequestUseCase
{
    /**
     * @param ProducerInterface $producer
     */
    public function __construct(
        private ProducerInterface $producer,
    ) {
    }

    /**
     * @param SendBankStatementRequestRequest $request
     * @return SendBankStatementRequestResponse
     */
    public function __invoke(SendBankStatementRequestRequest $request): SendBankStatementRequestResponse
    {
        $bankStatementMessage = new BankStatementMessage(
            $request->getEmail(),
            $request->getStartDate(),
            $request->getEndDate()
        );

        $this->producer->publish($bankStatementMessage);

        $message = 'Your bank statement request has been successfully accepted.';
        return new SendBankStatementRequestResponse($message);
    }
}
