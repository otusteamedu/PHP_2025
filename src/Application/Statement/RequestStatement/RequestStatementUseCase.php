<?php
declare(strict_types=1);

namespace Dinargab\Homework20\Application\Statement\RequestStatement;

use Dinargab\Homework20\Domain\Job\Factory\JobFactoryInterface;
use Dinargab\Homework20\Domain\Job\JobStatusEnum;
use Dinargab\Homework20\Domain\Job\Repository\JobRepositoryInterface;
use Dinargab\Homework20\Domain\Queue\QueueServiceInterface;

class RequestStatementUseCase
{

    public function __construct(
        public readonly JobRepositoryInterface $jobRepository,
        public readonly QueueServiceInterface  $queueRepository,
        public readonly JobFactoryInterface    $jobFactory,
    )
    {

    }

    public function __invoke(RequestStatementRequest $requestStatementRequest): RequestStatementResponse
    {
        $job = $this->jobFactory->create($requestStatementRequest->dateFrom, $requestStatementRequest->dateTo);
        $this->jobRepository->addJob($job);
        $this->queueRepository->push($job);
        $this->jobRepository->setStatus($job, JobStatusEnum::ACTIVE);

        return new RequestStatementResponse($job->getId());
    }

}