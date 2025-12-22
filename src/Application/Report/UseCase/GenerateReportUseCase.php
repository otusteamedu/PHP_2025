<?php
declare(strict_types=1);

namespace Dinargab\Homework19\Application\Report\UseCase;

use Dinargab\Homework19\Domain\Queue\Repository\QueueRepositoryInterface;
use Dinargab\Homework19\Domain\Request\Factory\ReportRequestFactoryInterface;
use InvalidArgumentException;

class GenerateReportUseCase
{
    public function __construct(
        private readonly ReportRequestFactoryInterface $reportRequestFactory,
        private readonly QueueRepositoryInterface      $queueRepository,
    )
    {

    }

    public function __invoke(GenerateReportRequest $generateReportRequest): GenerateReportResponse
    {
        try {
            $reportRequest = $this->reportRequestFactory->create(
                $generateReportRequest->dateFrom,
                $generateReportRequest->dateTo,
                $generateReportRequest->email
            );
            $this->queueRepository->push($reportRequest);
            $message = "Request #". $reportRequest->getId() . " added to queue. You will receive an email when your request will be processed.";
            return new GenerateReportResponse($message);
        } catch (InvalidArgumentException $exception) {
            return new GenerateReportResponse($exception->getMessage());
        }
    }
}