<?php

declare(strict_types=1);

namespace App\Service;

use App\Enum\QueueNameEnum;
use App\Exception\CustomException;
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
            $startDate = $entryData['startDate'] ?? null;
            $endDate = $entryData['endDate'] ?? null;
            $email = $entryData['email'] ?? null;

            if (empty($startDate) || empty($endDate) || empty($email)) {
                throw new CustomException('Invalid entry data', 400);
            }

            $startDate = (new DateTimeImmutable($entryData['startDate']))->format('Y-m-d H:i:s');
            $endDate = (new DateTimeImmutable($entryData['endDate']))->format('Y-m-d H:i:s');
            $email = $entryData['email'];
        } catch (Throwable) {
            throw new CustomException('Invalid entry data', 400);
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
