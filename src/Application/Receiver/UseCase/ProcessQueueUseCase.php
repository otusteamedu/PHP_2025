<?php
declare(strict_types=1);

namespace Dinargab\Homework19\Application\Receiver\UseCase;

use Dinargab\Homework19\Domain\Console\ConsoleLoggerInterface;
use Dinargab\Homework19\Domain\Notification\NotificationInterface;
use Dinargab\Homework19\Domain\Queue\Repository\QueueRepositoryInterface;
use Dinargab\Homework19\Domain\Request\Entity\ReportRequest;

class ProcessQueueUseCase
{
    public function __construct(
        private readonly QueueRepositoryInterface $queueRepository,
        private readonly NotificationInterface $notification,
        private readonly ConsoleLoggerInterface $consoleLogger
    )
    {

    }

    public function __invoke()
    {
        $callback = function (ReportRequest $reportRequest) {
            $this->notification->send(
                $reportRequest->getNotificationEmail()->getValue(),
                "Your request #{$reportRequest->getId()} has been received and processed.",
                "Some data from {$reportRequest->getStartDate()->format("Y-m-d")} to {$reportRequest->getEndDate()->format("Y-m-d")}. Here are the details:\n DETAILS!"
            );
            $this->consoleLogger->log("Request #{$reportRequest->getId()} processed successfully");
        };
        $this->queueRepository->pull($callback);
    }
}