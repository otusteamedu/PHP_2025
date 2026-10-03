<?php

declare(strict_types=1);

namespace App\Domain\BankReport;

use App\Domain\BankReport\Contract\ReportRequestPublisherInterface;
use App\Domain\BankReport\Exception\ReportRequestException;
use App\Domain\BankReport\ValueObject\Report;
use App\Domain\BankReport\ValueObject\ReportGenerationRequest;

class ReportService
{
    public function __construct(
        private readonly ReportRequestPublisherInterface $publisher,
    ) {
    }

    /**
     * @throws ReportRequestException
     */
    public function requestAsync(ReportGenerationRequest $request): void
    {
        $this->publisher->publish($request);
    }

    public function generateReport(ReportGenerationRequest $request): Report
    {
        $header = sprintf(
            'Выписка по клиенту "%s" с %s по %s',
            $request->clientName->value,
            $request->dateRange->dateFrom->format('d-m-Y'),
            $request->dateRange->dateTo->format('d-m-Y'),
        );

        $body = sprintf(
            'Банковская выписка по клиенту "%s" за период с %s по %s. Тип отчёта: %s. Email для отправки: %s',
            $request->clientName->value,
            $request->dateRange->dateFrom->format('d-m-Y'),
            $request->dateRange->dateTo->format('d-m-Y'),
            $request->reportType->getLowercaseLabel(),
            $request->email->value,
        );

        // Имитация долгой генерации
        sleep(mt_rand(5, 10));

        return new Report($request->reportId, $header, $body);
    }
}
