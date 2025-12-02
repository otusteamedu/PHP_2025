<?php
declare(strict_types=1);

namespace Dinargab\Homework20\Application\Job\GetJobs;

use Dinargab\Homework20\Domain\Job\Repository\JobRepositoryInterface;

class GetAllJobsUseCase
{
    public function __construct(
        private JobRepositoryInterface $jobRepository,
    )
    {

    }

    public function __invoke()
    {
        return $this->jobRepository->getJobs();
    }
}