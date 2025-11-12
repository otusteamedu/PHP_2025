<?php

namespace App\Command;

use App\Service\ReportService;
use Exception;
use JsonException;
use PhpAmqpLib\Message\AMQPMessage;

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
    public function process(AMQPMessage $queueMessage): void
    {
        $decodedData = json_decode($queueMessage->getBody(), true, 512, JSON_THROW_ON_ERROR);

        if (!isset($decodedData['email'], $decodedData['startDate'], $decodedData['endDate'])) {
            throw new Exception('Invalid queue data');
        }

        $this->reportService->prepareReportData($decodedData['email'], $decodedData['startDate'], $decodedData['endDate']);

        //send notification
    }
}
