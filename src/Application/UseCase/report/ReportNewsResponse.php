<?php

namespace App\Application\UseCase\report;

class ReportNewsResponse
{
    public function __construct(
        public readonly string $reportPath,
    )
    {

    }
}