<?php
declare(strict_types=1);

namespace Dinargab\Homework20\Application\Job\GetJobStatus;

use Dinargab\Homework20\Domain\Job\JobStatusEnum;

class GetJobStatusResponse
{

    public function __construct(
        private int           $jobId,
        private JobStatusEnum $status,
        private               $statementId = null
    )
    {
    }

    public function toArray(): array
    {
        $result = [
            'job_id' => $this->jobId,
            'status' => $this->status->value,
        ];

        if ($this->statementId !== null) {
            $result['statementId'] = $this->statementId;
        }
        return $result;
    }

    public function getJobId(): int
    {
        return $this->jobId;
    }

    public function getStatus(): JobStatusEnum
    {
        return $this->status;
    }

    public function getStatementId(): ?int
    {
        return $this->statementId;
    }

}