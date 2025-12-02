<?php
declare(strict_types=1);

namespace Dinargab\Homework20\Application\Job\GetJobStatus;

use Dinargab\Homework20\Domain\Exception\EntityNotFoundException;
use Dinargab\Homework20\Domain\Job\Repository\JobRepositoryInterface;

class GetJobStatusUseCase
{
    public function __construct(
        public JobRepositoryInterface $jobRepository,
    )
    {

    }

    public function __invoke(GetJobStatusRequest $request): GetJobStatusResponse
    {
        $job = $this->jobRepository->getJobById($request->getJobId());
        if ($job === null) {
            throw new EntityNotFoundException("Job with id '{$request->getJobId()}' not found");
        }
        return new GetJobStatusResponse(
            $job->getId(),
            $job->getStatus(),
            $job->getId()
        );
    }
}