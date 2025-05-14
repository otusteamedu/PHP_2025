<?php declare(strict_types=1);

namespace App\Application\DTO\Report;

final class ResponseReportDTO
{
    public readonly string $reportPath;
    public readonly string $message;

    public function __construct(string $reportPath)
    {
        $this->reportPath = $reportPath;
        $this->message = 'Order successfully generated';
    }
}