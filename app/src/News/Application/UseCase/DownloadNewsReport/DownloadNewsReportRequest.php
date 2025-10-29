<?php

declare(strict_types=1);

namespace App\News\Application\UseCase\DownloadNewsReport;

class DownloadNewsReportRequest
{
    public function __construct(public string $fileName)
    {
    }
}
