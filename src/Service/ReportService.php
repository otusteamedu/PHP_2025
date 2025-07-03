<?php
declare(strict_types=1);

namespace App\Service;

use App\Repository\ReportRepository;
use Redis;

class ReportService
{
    public function __construct(
        private ReportRepository $repository,
        private Redis $redis,
    ) {}

    public function enqueueReport(): int
    {
        $id = $this->repository->create();
        $this->redis->rPush('report_queue', $id);
        return $id;
    }

    public function getReportStatus(int $id): ?string
    {
        return $this->repository->getStatus($id);
    }

    public function processReport(int $id): void
    {
        // Заглушка генерации отчета
        sleep(2);
        $this->repository->markCompleted($id);
    }
}
