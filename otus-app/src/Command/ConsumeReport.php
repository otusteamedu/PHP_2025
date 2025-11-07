<?php

namespace App\Controller;

use App\Interface\QueueServiceInterface;
use App\Service\ReportService;
use Exception;
use JsonException;

class ConsumeReport
{
    public function __construct(
        private ReportService $reportService,
    ) {
    }

    /**
     * @throws JsonException
     * @throws Exception
     */
    public function process(string $queueMessage): void
    {
        $decodedData = json_decode($queueMessage, true, 512, JSON_THROW_ON_ERROR);

        if (!isset($decodedData['email'], $decodedData['startDate'], $decodedData['endDate'])) {
            throw new Exception('Invalid queue data');
        }

        $this->reportService->prepareReportData($decodedData['email'], $decodedData['startDate'], $decodedData['endDate']);

        //send notification
    }
}
