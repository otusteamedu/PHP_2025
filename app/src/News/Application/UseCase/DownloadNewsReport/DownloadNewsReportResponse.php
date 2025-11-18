<?php

declare(strict_types=1);

namespace App\News\Application\UseCase\DownloadNewsReport;

class DownloadNewsReportResponse
{
    public function __construct(public $stream, public string $mimeType)
    {
    }
}
