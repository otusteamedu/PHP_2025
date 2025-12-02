<?php
declare(strict_types=1);

namespace Dinargab\Homework20\Domain\Job\Repository;

use Dinargab\Homework20\Domain\Job\Entity\Job;
use Dinargab\Homework20\Domain\Job\JobStatusEnum;

interface JobRepositoryInterface
{
    public function getJobs(): array;

    public function getJobById(int $jobId): ?Job;

    public function update(Job $job): void;
    public function setStatus(Job $job, JobStatusEnum $statusEnum): Job;
}