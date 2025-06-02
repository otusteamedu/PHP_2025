<?php

declare(strict_types=1);

namespace App\Application\UseCase\SendBankStatementRequest;

use DateTimeImmutable;
use Exception;
use InvalidArgumentException;

/**
 * Class SendBankStatementRequestValidator
 * @package App\Application\UseCase\SendBankStatementRequest
 */
readonly class SendBankStatementRequestValidator
{
    /**
     * @param SendBankStatementRequestRequest $request
     * @return void
     * @throws Exception
     */
    public function validate(SendBankStatementRequestRequest $request): void
    {
        $email = $request->getEmail();
        $startDate = $request->getStartDate();
        $endDate = $request->getEndDate();

        if (empty($email)) {
            throw new InvalidArgumentException('Empty email');
        }

        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            throw new InvalidArgumentException('Incorrect email: ' . $email);
        }

        if (empty($startDate)) {
            throw new InvalidArgumentException('Empty start date');
        }

        if (empty($endDate)) {
            throw new InvalidArgumentException('Empty end date');
        }

        $startDate = new DateTimeImmutable($startDate);
        $endDate = new DateTimeImmutable($endDate);

        if ($startDate > $endDate) {
            throw new InvalidArgumentException('Start date must be before end date');
        }
    }
}
