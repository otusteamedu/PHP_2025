<?php

declare(strict_types=1);

namespace App\News\Application\UseCase\GenerateNewsReport;

class GenerateNewsReportResponse
{
    public function __construct(public string $pathToFile)
    {
    }
}
