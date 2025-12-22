<?php
declare(strict_types=1);

namespace Dinargab\Homework19\Application\Report\UseCase;

class GenerateReportRequest
{
    public function __construct(
        public readonly string $dateFrom,
        public readonly string $dateTo,
        public readonly string $email,
    )
    {

    }

}