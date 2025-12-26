<?php
declare(strict_types=1);

namespace Dinargab\Homework20\Application\Job\GetJobStatus;

class GetJobStatusRequest
{


    public function __construct(private int $jobId)
    {
    }

    public function getJobId(): int
    {
        return $this->jobId;
    }
}