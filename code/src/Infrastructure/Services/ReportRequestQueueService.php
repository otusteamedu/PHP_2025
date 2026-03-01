<?php

declare(strict_types=1);

namespace App\Infrastructure\Services;

use App\Domain\Entities\ReportRequest;
use App\Domain\Interfaces\MessagePublisherInterface;
use App\Domain\Interfaces\ReportRequestServiceInterface;

class ReportRequestQueueService implements ReportRequestServiceInterface
{
    private const QUEUE_NAME = 'bank_reports';

    public function __construct(
        private readonly MessagePublisherInterface $publisher
    ) {}

    public function queueReportRequest(ReportRequest $request): void
    {
        $this->publisher->publish(
            queue: self::QUEUE_NAME,
            message: $request->toArray()
        );
    }
}
