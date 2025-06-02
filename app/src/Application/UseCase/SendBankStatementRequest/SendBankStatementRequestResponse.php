<?php

declare(strict_types=1);

namespace App\Application\UseCase\SendBankStatementRequest;

/**
 * Class SendBankStatementRequestResponse
 * @package App\Application\UseCase\SendBankStatementRequest
 */
readonly class SendBankStatementRequestResponse
{
    /**
     * @param string $message
     */
    public function __construct(
        private string $message,
    ) {
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }
}
