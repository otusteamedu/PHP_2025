<?php

declare(strict_types=1);

namespace App\Domain\BankReport\Contract;

use App\Domain\BankReport\Exception\ReportRequestException;
use App\Domain\BankReport\ValueObject\ReportGenerationRequest;

interface ReportRequestPublisherInterface
{
    /**
     * @throws ReportRequestException
     */
    public function publish(ReportGenerationRequest $request): void;
}
