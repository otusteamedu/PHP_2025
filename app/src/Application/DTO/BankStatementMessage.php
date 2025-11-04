<?php

declare(strict_types=1);

namespace App\Application\DTO;

use Stringable;

/**
 * Class BankStatementMessage
 * @package App\Application\DTO
 */
readonly class BankStatementMessage implements Stringable
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

    /**
     * @return string
     */
    public function __toString(): string
    {
        return json_encode([
            'email' => $this->email,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
        ]);
    }
}
