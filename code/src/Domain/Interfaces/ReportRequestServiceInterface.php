<?php

declare(strict_types=1);

namespace App\Domain\Interfaces;

use App\Domain\Entities\ReportRequest;

interface ReportRequestServiceInterface
{
    /**
     * Добавляет запрос на формирование отчета в очередь
     */
    public function queueReportRequest(ReportRequest $request): void;
}
