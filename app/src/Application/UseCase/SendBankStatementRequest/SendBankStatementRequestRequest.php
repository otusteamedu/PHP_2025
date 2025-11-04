<?php

declare(strict_types=1);

namespace App\Application\UseCase\SendBankStatementRequest;

/**
 * Class SendBankStatementRequestRequest
 * @package App\Application\UseCase\SendBankStatementRequest
 */
readonly class SendBankStatementRequestRequest
{
    /**
     * @param string $email
     * @param string $startDate
     * @param string $endDate
     */
    public function __construct(
        private string $email,
        private string $startDate,
        private string $endDate,
    ) {
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getStartDate(): string
    {
        return $this->startDate;
    }

    /**
     * @return string
     */
    public function getEndDate(): string
    {
        return $this->endDate;
    }
}
