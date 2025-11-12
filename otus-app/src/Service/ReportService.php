<?php

namespace App\Service;

use App\Enum\QueueNameEnum;
use App\Interface\QueueServiceInterface;
use DateTimeImmutable;
use Exception;
use JsonException;
use Throwable;

class ReportService
{
    public function __construct(
        private QueueServiceInterface $queueService,
    ) {
    }

    /**
     * @throws JsonException
     * @throws Exception
     */
    public function addCreateReportEvent(array $entryData): void
    {
        try {
            $startDate = (new DateTimeImmutable($entryData['startDate']))->format('Y-m-d H:i:s');
            $endDate = (new DateTimeImmutable($entryData['endDate']))->format('Y-m-d H:i:s');
            $email = $entryData['email'];
        } catch (Throwable) {
            throw new Exception('Invalid entry data');
        }

        $queueData = [
            'email' => $email,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ];

        $this->queueService->publish(
            QueueNameEnum::REPORT_QUEUE,
            json_encode($queueData, JSON_THROW_ON_ERROR),
        );
    }

    public function prepareReportData(string $email, string $startDate, string $endDate): array
    {
        $reportData = ['some report data', $email, $startDate, $endDate];
        $file = fopen('php://output', 'w');
        sleep(5);

        fputcsv($file, $reportData, escape: "\\");

        return $reportData;
    }
}
